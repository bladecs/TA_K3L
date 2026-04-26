<?php

namespace App\Http\Controllers\User;

use App\Actions\Incidents\CreateIncidentReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Incident\StoreIncidentReportRequest;
use App\Models\IncidentReport;
use App\Support\Reports\ReportFormOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncidentReportController extends Controller
{
    public function __construct(
        protected CreateIncidentReport $createIncidentReport,
        protected ReportFormOptions $reportFormOptions,
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', IncidentReport::class);

        $selectedQuery = trim((string) $request->string('q'));
        $selectedStatus = trim((string) $request->string('status'));

        $baseQuery = IncidentReport::query()
            ->where('reported_by', $request->user()->id);

        $reports = IncidentReport::query()
            ->with(['category', 'location'])
            ->where('reported_by', $request->user()->id)
            ->when($selectedQuery !== '', function ($query) use ($selectedQuery) {
                $query->where(function ($subQuery) use ($selectedQuery) {
                    $subQuery
                        ->where('report_number', 'like', '%' . $selectedQuery . '%')
                        ->orWhere('title', 'like', '%' . $selectedQuery . '%')
                        ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', '%' . $selectedQuery . '%'))
                        ->orWhereHas('location', fn ($locationQuery) => $locationQuery->where('name', 'like', '%' . $selectedQuery . '%'));
                });
            })
            ->when($selectedStatus !== '', fn ($query) => $query->where('status', $selectedStatus))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $summaryCounts = [
            'total_reports' => (clone $baseQuery)->count(),
            'submitted_reports' => (clone $baseQuery)->where('status', 'submitted')->count(),
            'closed_reports' => (clone $baseQuery)->where('status', 'closed')->count(),
        ];

        return view('user.incidents.index', compact('reports', 'summaryCounts', 'selectedQuery', 'selectedStatus'));
    }

    public function status(Request $request): View
    {
        $this->authorize('viewAny', IncidentReport::class);

        $selectedQuery = trim((string) $request->string('q'));
        $selectedStatus = trim((string) $request->string('status'));

        $reports = IncidentReport::query()
            ->with(['category', 'location'])
            ->where('reported_by', $request->user()->id)
            ->latest()
            ->get();

        $filteredReports = $reports
            ->when($selectedQuery !== '', function ($collection) use ($selectedQuery) {
                $needle = mb_strtolower($selectedQuery);

                return $collection->filter(function ($report) use ($needle) {
                    return str_contains(mb_strtolower((string) $report->report_number), $needle)
                        || str_contains(mb_strtolower((string) $report->title), $needle)
                        || str_contains(mb_strtolower((string) optional($report->category)->name), $needle)
                        || str_contains(mb_strtolower((string) optional($report->location)->name), $needle);
                });
            })
            ->when($selectedStatus !== '', fn ($collection) => $collection->where('status', $selectedStatus))
            ->values();

        $statusCounts = collect([
            'draft' => $reports->where('status', 'draft')->count(),
            'submitted' => $reports->where('status', 'submitted')->count(),
            'verified' => $reports->where('status', 'verified')->count(),
            'investigating' => $reports->where('status', 'investigating')->count(),
            'resolved' => $reports->where('status', 'resolved')->count(),
            'closed' => $reports->where('status', 'closed')->count(),
            'rejected' => $reports->where('status', 'rejected')->count(),
        ]);

        $statusBoard = [
            [
                'title' => 'Menunggu Review',
                'status' => 'submitted',
                'count' => $statusCounts->get('submitted'),
                'description' => 'Laporan sudah masuk dan sedang menunggu validasi awal dari Satgas.',
                'accent' => 'bg-amber-100 text-amber-700',
            ],
            [
                'title' => 'Terverifikasi',
                'status' => 'verified',
                'count' => $statusCounts->get('verified'),
                'description' => 'Laporan sudah lolos validasi awal dan siap masuk tindak lanjut.',
                'accent' => 'bg-emerald-100 text-emerald-700',
            ],
            [
                'title' => 'Sedang Ditindaklanjuti',
                'status' => 'investigating',
                'count' => $statusCounts->get('investigating'),
                'description' => 'Satgas sedang melakukan penelusuran dan penanganan lapangan.',
                'accent' => 'bg-sky-100 text-sky-700',
            ],
            [
                'title' => 'Tindakan Selesai',
                'status' => 'resolved',
                'count' => $statusCounts->get('resolved'),
                'description' => 'Tindakan perbaikan sudah dijalankan dan menunggu penutupan akhir.',
                'accent' => 'bg-indigo-100 text-indigo-700',
            ],
            [
                'title' => 'Selesai',
                'status' => 'closed',
                'count' => $statusCounts->get('closed'),
                'description' => 'Kasus telah diselesaikan dan laporan resmi ditutup.',
                'accent' => 'bg-slate-200 text-slate-700',
            ],
        ];

        $recentStatusReports = $filteredReports->take(6);

        return view('user.incidents.status', compact(
            'reports',
            'statusCounts',
            'statusBoard',
            'recentStatusReports',
            'selectedQuery',
            'selectedStatus',
        ));
    }

    public function create(): View
    {
        $this->authorize('create', IncidentReport::class);

        return view('user.incidents.create', $this->reportFormOptions->incident());
    }

    public function show(IncidentReport $incidentReport): View
    {
        $this->authorize('view', $incidentReport);

        $incidentReport->load([
            'category',
            'location',
            'reporter',
            'victim',
            'attachments',
            'statusHistories.changer',
            'followUps.actionOwner',
            'followUps.creator',
        ]);

        return view('user.incidents.show', compact('incidentReport'));
    }

    public function store(StoreIncidentReportRequest $request): RedirectResponse
    {
        $this->authorize('create', IncidentReport::class);

        $report = $this->createIncidentReport->handle(
            $request->safe()->except('victim_type'),
            $request->user()->id,
        );

        return redirect()
            ->route('user.incidents.index')
            ->with('status', "Laporan {$report->report_number} berhasil dikirim dan menunggu verifikasi Satgas.");
    }
}
