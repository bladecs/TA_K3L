<?php

namespace App\Http\Controllers\Satgas;

use App\Actions\Incidents\VerifyIncidentReport;
use App\Actions\Incidents\CreateIncidentFollowUp;
use App\Actions\Incidents\UpdateIncidentReportStatus;
use App\Exports\IncidentGisSpreadsheetExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Incident\StoreIncidentFollowUpRequest;
use App\Http\Requests\Incident\UpdateIncidentStatusRequest;
use App\Http\Requests\Incident\VerifyIncidentReportRequest;
use App\Models\BodyPart;
use App\Models\IncidentCategory;
use App\Models\IncidentReport;
use App\Models\InjuryCategory;
use App\Models\Location;
use App\Models\User;
use App\Support\Hazards\PublicHazardMapData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
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

    public function gis(Request $request): View
    {
        $filters = $this->gisFilters($request);
        $baseQuery = $this->incidentGisQuery($filters);

        $reports = (clone $baseQuery)
            ->latest('incident_date')
            ->paginate(15)
            ->withQueryString();

        $mapReports = (clone $baseQuery)
            ->latest('incident_date')
            ->limit(500)
            ->get();

        $markers = $mapReports
            ->map(fn (IncidentReport $report) => $this->incidentMarker($report))
            ->filter()
            ->values();

        $summary = [
            'total' => (clone $baseQuery)->count(),
            'inside' => (clone $baseQuery)->where(function (Builder $query) {
                $query
                    ->whereHas('verifiedLocation', fn (Builder $location) => $location->where('name', '!=', 'Diluar Polman'))
                    ->orWhere(function (Builder $subQuery) {
                        $subQuery
                            ->whereNull('verified_location_id')
                            ->whereHas('location', fn (Builder $location) => $location->where('name', '!=', 'Diluar Polman'));
                    });
            })->count(),
            'outside' => (clone $baseQuery)->where(function (Builder $query) {
                $query
                    ->whereHas('verifiedLocation', fn (Builder $location) => $location->where('name', 'Diluar Polman'))
                    ->orWhere(function (Builder $subQuery) {
                        $subQuery
                            ->whereNull('verified_location_id')
                            ->whereHas('location', fn (Builder $location) => $location->where('name', 'Diluar Polman'));
                    });
            })->count(),
        ];

        $locations = Location::query()->where('is_active', true)->orderBy('name')->get();
        $categories = IncidentCategory::query()->orderBy('name')->get();
        $campusBuildingPolygons = app(PublicHazardMapData::class)->campusBuildingPolygons();

        return view('satgas.incidents.gis', compact(
            'reports',
            'markers',
            'summary',
            'filters',
            'locations',
            'categories',
            'campusBuildingPolygons',
        ));
    }

    public function exportGis(Request $request, IncidentGisSpreadsheetExport $export): StreamedResponse
    {
        $filters = $this->gisFilters($request);
        $reports = $this->incidentGisQuery($filters)
            ->latest('incident_date')
            ->get();

        return $export->download($reports, $filters);
    }

    public function show(IncidentReport $incidentReport): View
    {
        $this->authorize('view', $incidentReport);

        $incidentReport->load([
            'category',
            'injuryCategory',
            'bodyPart',
            'injuries.injuryCategory',
            'injuries.bodyPart',
            'location',
            'campusRoom',
            'verifiedLocation',
            'locationVerifier',
            'reporter',
            'victim',
            'attachments',
            'statusHistories.changer',
            'followUps.actionOwner',
            'followUps.creator',
        ]);

        $injuryCategories = InjuryCategory::query()->orderBy('name')->get();
        $bodyParts = BodyPart::query()->orderBy('name')->get();
        $categories = IncidentCategory::query()->orderBy('name')->get();
        $locations = Location::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $campusBuildingPolygons = app(PublicHazardMapData::class)->campusBuildingPolygons();

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
            'categories',
            'locations',
            'campusBuildingPolygons',
        ));
    }

    public function verify(VerifyIncidentReportRequest $request, IncidentReport $incidentReport): RedirectResponse
    {
        $incidentReport = $this->verifyIncidentReport->handle(
            $incidentReport,
            $request->user()->id,
            $request->string('verification_note')->toString() ?: null,
            $request->safe()->only([
                'title',
                'incident_category_id',
                'severity_level',
                'injury_category_id',
                'body_part_id',
                'impact',
                'unsafe_conditions',
                'unsafe_actions',
                'unsafe_condition_cause',
                'unsafe_action_cause',
                'warning_given_before_incident',
                'incident_previously_occurred',
                'proposed_preventions',
                'prevention_action_plan',
                'verified_location_id',
                'verified_specific_location',
                'verified_latitude',
                'verified_longitude',
                'verified_location_accuracy',
            ]),
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

    protected function gisFilters(Request $request): array
    {
        return [
            'q' => trim((string) $request->string('q')),
            'status' => trim((string) $request->string('status')),
            'category_id' => trim((string) $request->string('category_id')),
            'location_id' => trim((string) $request->string('location_id')),
            'severity_level' => trim((string) $request->string('severity_level')),
            'scope' => trim((string) $request->string('scope')),
            'date_from' => trim((string) $request->string('date_from')),
            'date_to' => trim((string) $request->string('date_to')),
            'month' => trim((string) $request->string('month')),
            'year' => trim((string) $request->string('year')),
        ];
    }

    protected function incidentGisQuery(array $filters): Builder
    {
        return IncidentReport::query()
            ->with([
                'category',
                'location',
                'verifiedLocation',
                'reporter',
                'injuries.injuryCategory',
                'injuries.bodyPart',
            ])
            ->where(function (Builder $query) {
                $query
                    ->where(function (Builder $subQuery) {
                        $subQuery->whereNotNull('verified_latitude')->whereNotNull('verified_longitude');
                    })
                    ->orWhere(function (Builder $subQuery) {
                        $subQuery->whereNotNull('latitude')->whereNotNull('longitude');
                    });
            })
            ->when($filters['q'] !== '', function (Builder $query) use ($filters) {
                $query->where(function (Builder $subQuery) use ($filters) {
                    $subQuery
                        ->where('report_number', 'like', '%' . $filters['q'] . '%')
                        ->orWhere('title', 'like', '%' . $filters['q'] . '%')
                        ->orWhere('reporter_name', 'like', '%' . $filters['q'] . '%')
                        ->orWhere('victim_name', 'like', '%' . $filters['q'] . '%')
                        ->orWhereHas('reporter', fn (Builder $reporter) => $reporter->where('name', 'like', '%' . $filters['q'] . '%'));
                });
            })
            ->when($filters['status'] !== '', fn (Builder $query) => $query->where('status', $filters['status']))
            ->when($filters['category_id'] !== '', fn (Builder $query) => $query->where('incident_category_id', $filters['category_id']))
            ->when($filters['location_id'] !== '', function (Builder $query) use ($filters) {
                $query->where(function (Builder $subQuery) use ($filters) {
                    $subQuery
                        ->where('verified_location_id', $filters['location_id'])
                        ->orWhere(function (Builder $fallback) use ($filters) {
                            $fallback->whereNull('verified_location_id')->where('location_id', $filters['location_id']);
                        });
                });
            })
            ->when($filters['severity_level'] !== '', fn (Builder $query) => $query->where('severity_level', $filters['severity_level']))
            ->when($filters['scope'] === 'inside', function (Builder $query) {
                $query->where(function (Builder $subQuery) {
                    $subQuery
                        ->whereHas('verifiedLocation', fn (Builder $location) => $location->where('name', '!=', 'Diluar Polman'))
                        ->orWhere(function (Builder $fallback) {
                            $fallback
                                ->whereNull('verified_location_id')
                                ->whereHas('location', fn (Builder $location) => $location->where('name', '!=', 'Diluar Polman'));
                        });
                });
            })
            ->when($filters['scope'] === 'outside', function (Builder $query) {
                $query->where(function (Builder $subQuery) {
                    $subQuery
                        ->whereHas('verifiedLocation', fn (Builder $location) => $location->where('name', 'Diluar Polman'))
                        ->orWhere(function (Builder $fallback) {
                            $fallback
                                ->whereNull('verified_location_id')
                                ->whereHas('location', fn (Builder $location) => $location->where('name', 'Diluar Polman'));
                        });
                });
            })
            ->when($filters['date_from'] !== '', fn (Builder $query) => $query->whereDate('incident_date', '>=', $filters['date_from']))
            ->when($filters['date_to'] !== '', fn (Builder $query) => $query->whereDate('incident_date', '<=', $filters['date_to']))
            ->when($filters['month'] !== '', fn (Builder $query) => $query->whereMonth('incident_date', $filters['month']))
            ->when($filters['year'] !== '', fn (Builder $query) => $query->whereYear('incident_date', $filters['year']));
    }

    protected function incidentMarker(IncidentReport $report): ?array
    {
        $latitude = $report->verified_latitude ?? $report->latitude;
        $longitude = $report->verified_longitude ?? $report->longitude;

        if ($latitude === null || $longitude === null) {
            return null;
        }

        $locationName = $report->verifiedLocation?->name ?? $report->location?->name ?? '-';
        $specificLocation = $report->verified_specific_location ?? $report->specific_location ?? '-';

        return [
            'id' => $report->id,
            'report_number' => $report->report_number,
            'title' => $report->title,
            'reporter' => $report->reporter?->name ?? $report->reporter_name ?? '-',
            'location' => $locationName,
            'specific_location' => $specificLocation,
            'category' => $report->category?->name ?? '-',
            'severity_level' => $report->severity_level ?: '-',
            'status' => $report->status,
            'incident_date' => optional($report->incident_date)->format('d M Y'),
            'latitude' => (float) $latitude,
            'longitude' => (float) $longitude,
            'scope' => $locationName === 'Diluar Polman' ? 'outside' : 'inside',
            'show_url' => route('satgas.incidents.show', $report),
        ];
    }
}
