<?php

namespace App\Http\Controllers\Satgas;

use App\Actions\Incidents\VerifyIncidentReport;
use App\Actions\Incidents\CreateIncidentFollowUp;
use App\Actions\Incidents\UpdateIncidentReportStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Incident\StoreIncidentFollowUpRequest;
use App\Http\Requests\Incident\UpdateIncidentStatusRequest;
use App\Http\Requests\Incident\VerifyIncidentReportRequest;
use App\Models\BodyPart;
use App\Models\IncidentReport;
use App\Models\InjuryCategory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncidentReviewController extends Controller
{
    public function __construct(
        protected VerifyIncidentReport $verifyIncidentReport,
        protected UpdateIncidentReportStatus $updateIncidentReportStatus,
        protected CreateIncidentFollowUp $createIncidentFollowUp,
    ) {
    }

    public function index(Request $request): View
    {
        $baseQuery = IncidentReport::query();
        $selectedQuery = trim((string) $request->string('q'));
        $selectedStatus = trim((string) $request->string('status'));

        $reports = IncidentReport::query()
            ->with(['category', 'location', 'reporter'])
            ->when($selectedQuery !== '', function ($query) use ($selectedQuery) {
                $query->where(function ($subQuery) use ($selectedQuery) {
                    $subQuery
                        ->where('report_number', 'like', '%' . $selectedQuery . '%')
                        ->orWhere('title', 'like', '%' . $selectedQuery . '%')
                        ->orWhereHas('reporter', fn ($reporterQuery) => $reporterQuery->where('name', 'like', '%' . $selectedQuery . '%'))
                        ->orWhereHas('location', fn ($locationQuery) => $locationQuery->where('name', 'like', '%' . $selectedQuery . '%'));
                });
            })
            ->when($selectedStatus !== '', fn ($query) => $query->where('status', $selectedStatus))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $summaryCounts = [
            'submitted' => (clone $baseQuery)->where('status', 'submitted')->count(),
            'verified' => (clone $baseQuery)->where('status', 'verified')->count(),
            'investigating' => (clone $baseQuery)->where('status', 'investigating')->count(),
            'resolved' => (clone $baseQuery)->where('status', 'resolved')->count(),
            'closed' => (clone $baseQuery)->where('status', 'closed')->count(),
        ];

        return view('satgas.incidents.index', compact('reports', 'summaryCounts', 'selectedQuery', 'selectedStatus'));
    }

    public function show(IncidentReport $incidentReport): View
    {
        $this->authorize('view', $incidentReport);

        $incidentReport->load([
            'category',
            'injuryCategory',
            'bodyPart',
            'location',
            'reporter',
            'victim',
            'attachments',
            'statusHistories.changer',
            'followUps.actionOwner',
            'followUps.creator',
        ]);

        $injuryCategories = InjuryCategory::query()->orderBy('name')->get();
        $bodyParts = BodyPart::query()->orderBy('name')->get();

        $statusOptions = match ($incidentReport->status) {
            'submitted' => ['rejected' => 'Rejected'],
            'verified' => ['investigating' => 'Investigating', 'resolved' => 'Resolved', 'rejected' => 'Rejected'],
            'investigating' => ['resolved' => 'Resolved', 'rejected' => 'Rejected'],
            'resolved' => ['closed' => 'Closed', 'investigating' => 'Investigating'],
            'rejected' => ['submitted' => 'Submitted'],
            default => [],
        };

        $followUpStatusOptions = [
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'done' => 'Done',
            'cancelled' => 'Cancelled',
        ];

        $assignableUsers = User::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('satgas.incidents.show', compact(
            'incidentReport',
            'statusOptions',
            'followUpStatusOptions',
            'assignableUsers',
            'injuryCategories',
            'bodyParts',
        ));
    }

    public function verify(VerifyIncidentReportRequest $request, IncidentReport $incidentReport): RedirectResponse
    {
        $incidentReport = $this->verifyIncidentReport->handle(
            $incidentReport,
            $request->user()->id,
            $request->string('verification_note')->toString() ?: null,
            $request->safe()->only(['injury_category_id', 'body_part_id', 'impact']),
        );

        return redirect()
            ->route('satgas.incidents.show', $incidentReport)
            ->with('status', "Laporan {$incidentReport->report_number} berhasil diverifikasi.");
    }

    public function updateStatus(UpdateIncidentStatusRequest $request, IncidentReport $incidentReport): RedirectResponse
    {
        $incidentReport = $this->updateIncidentReportStatus->handle(
            $incidentReport,
            $request->string('status')->toString(),
            $request->user()->id,
            $request->string('status_note')->toString() ?: null,
        );

        return redirect()
            ->route('satgas.incidents.show', $incidentReport)
            ->with('status', "Status laporan {$incidentReport->report_number} berhasil diperbarui ke {$incidentReport->status}.");
    }

    public function storeFollowUp(StoreIncidentFollowUpRequest $request, IncidentReport $incidentReport): RedirectResponse
    {
        $this->createIncidentFollowUp->handle(
            $incidentReport,
            $request->validated(),
            $request->user()->id,
        );

        return redirect()
            ->route('satgas.incidents.show', $incidentReport)
            ->with('status', "Tindak lanjut untuk laporan {$incidentReport->report_number} berhasil ditambahkan.");
    }
}
