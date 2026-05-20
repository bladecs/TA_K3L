<?php

namespace App\Http\Controllers\User;

use App\Actions\Hazards\CreatePotentialHazardReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hazard\StorePotentialHazardReportRequest;
use App\Models\PotentialHazardReport;
use App\Support\Reports\ReportFormOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PotentialHazardReportController extends Controller
{
    public function __construct(
        protected CreatePotentialHazardReport $createPotentialHazardReport,
        protected ReportFormOptions $reportFormOptions,
    ) {
    }

    public function __invoke(Request $request): View
    {
        return view('user.reports.create', array_merge(
            $this->reportFormOptions->combined(),
            ['activeReportType' => 'hazard'],
        ));
    }

    public function index(Request $request): View
    {
        $selectedQuery = trim((string) $request->string('q'));
        $selectedStatus = trim((string) $request->string('status'));

        $reports = PotentialHazardReport::query()
            ->with(['location', 'reviewer', 'resolver'])
            ->where('reported_by', $request->user()->id)
            ->when($selectedQuery !== '', function ($query) use ($selectedQuery) {
                $query->where(function ($subQuery) use ($selectedQuery) {
                    $subQuery
                        ->where('report_number', 'like', '%' . $selectedQuery . '%')
                        ->orWhere('title', 'like', '%' . $selectedQuery . '%')
                        ->orWhereHas('location', fn ($locationQuery) => $locationQuery->where('name', 'like', '%' . $selectedQuery . '%'));
                });
            })
            ->when($selectedStatus !== '', fn ($query) => $query->where('status', $selectedStatus))
            ->latest('submitted_at')
            ->paginate(10)
            ->withQueryString();

        $summaryCounts = collect(['submitted', 'reviewed', 'resolved'])
            ->mapWithKeys(fn (string $status) => [
                $status => PotentialHazardReport::query()
                    ->where('reported_by', $request->user()->id)
                    ->where('status', $status)
                    ->count(),
            ])
            ->all();

        return view('user.hazards.index', compact('reports', 'summaryCounts', 'selectedQuery', 'selectedStatus'));
    }

    public function map(): RedirectResponse
    {
        return redirect()->to(route('user.dashboard') . '#peta-gis-hazard');
    }

    public function show(Request $request, PotentialHazardReport $potentialHazardReport): View
    {
        abort_unless((int) $potentialHazardReport->reported_by === (int) $request->user()->id, 403);

        $potentialHazardReport->load([
            'location',
            'attachments',
            'reviewer',
            'resolver',
            'mapper',
        ]);

        return view('user.hazards.show', [
            'hazardReport' => $potentialHazardReport,
        ]);
    }

    public function store(StorePotentialHazardReportRequest $request): RedirectResponse
    {
        $report = $this->createPotentialHazardReport->handle(
            $request->validated(),
            $request->user()?->id,
        );

        return redirect()
            ->route('user.hazards.create')
            ->with('status', "Laporan potensi bahaya {$report->report_number} berhasil dikirim. Status akan diinformasikan melalui email dan WhatsApp yang Anda isi.");
    }

}
