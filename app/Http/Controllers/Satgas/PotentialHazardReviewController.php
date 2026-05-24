<?php

namespace App\Http\Controllers\Satgas;

use App\Actions\Hazards\UpdatePotentialHazardReportStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hazard\StoreHazardMapPointRequest;
use App\Http\Requests\Hazard\UpdateHazardPinpointRequest;
use App\Http\Requests\Hazard\UpdatePotentialHazardStatusRequest;
use App\Exports\HazardGisSpreadsheetExport;
use App\Models\HazardMapPoint;
use App\Models\PotentialHazardReport;
use App\Models\Location;
use App\Support\Hazards\PublicHazardMapData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PotentialHazardReviewController extends Controller
{
    public function __construct(
        protected UpdatePotentialHazardReportStatus $updatePotentialHazardReportStatus,
    ) {
    }

    public function index(Request $request): View
    {
        $selectedQuery = trim((string) $request->string('q'));
        $selectedStatus = trim((string) $request->string('status'));

        $reports = PotentialHazardReport::query()
            ->with(['reporter', 'location'])
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
            ->latest('submitted_at')
            ->paginate(10)
            ->withQueryString();

        $summaryCounts = collect(['submitted', 'reviewed', 'resolved'])
            ->mapWithKeys(fn (string $status) => [
                $status => PotentialHazardReport::query()->where('status', $status)->count(),
            ])
            ->all();

        return view('satgas.hazards.index', compact('reports', 'summaryCounts', 'selectedQuery', 'selectedStatus'));
    }

    public function map(Request $request): View
    {
        $filters = $this->gisFilters($request);

        $baseQuery = $this->hazardGisQuery($filters);

        $reports = (clone $baseQuery)
            ->latest('submitted_at')
            ->paginate(15)
            ->withQueryString();

        $mapReports = (clone $baseQuery)
            ->latest('submitted_at')
            ->limit(500)
            ->get();

        $hazardMarkers = $mapReports
            ->map(fn (PotentialHazardReport $report) => $this->hazardMarker($report))
            ->filter()
            ->values();

        $floorplanMarkers = PotentialHazardReport::query()
            ->with(['location'])
            ->whereNotNull('floorplan_x')
            ->whereNotNull('floorplan_y')
            ->latest('mapped_at')
            ->get()
            ->map(fn (PotentialHazardReport $report) => [
                'id' => $report->id,
                'report_number' => $report->report_number,
                'title' => $report->title,
                'location' => $report->location?->name ?? '-',
                'specific_location' => $report->specific_location ?: '-',
                'risk_level' => $report->risk_level ?: 'sedang',
                'status' => $report->status,
                'building_key' => 'gedung-teori',
                'floor' => 2,
                'x' => (float) $report->floorplan_x,
                'y' => (float) $report->floorplan_y,
                'show_url' => route('satgas.hazards.show', $report),
            ])
            ->values();

        $mapPointMarkers = HazardMapPoint::query()
            ->where('is_active', true)
            ->where('map_source', 'satellite')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest()
            ->get()
            ->map(fn (HazardMapPoint $point) => [
                'id' => $point->id,
                'title' => $point->title,
                'location' => 'Titik GIS Satgas',
                'specific_location' => $point->description ?: '-',
                'hazard_type' => $point->hazard_type ? str_replace('-', ' ', $point->hazard_type) : '-',
                'risk_level' => $point->risk_level,
                'status' => 'active',
                'latitude' => (float) $point->latitude,
                'longitude' => (float) $point->longitude,
                'show_url' => null,
                'source' => 'map_point',
            ])
            ->values();

        $floorplanMapPointMarkers = HazardMapPoint::query()
            ->where('is_active', true)
            ->where('map_source', 'floorplan')
            ->whereNotNull('floorplan_x')
            ->whereNotNull('floorplan_y')
            ->latest()
            ->get()
            ->map(fn (HazardMapPoint $point) => [
                'id' => $point->id,
                'title' => $point->title,
                'location' => 'Titik GIS Satgas',
                'specific_location' => $point->description ?: '-',
                'risk_level' => $point->risk_level,
                'status' => 'active',
                'building_key' => 'gedung-teori',
                'floor' => 2,
                'x' => (float) $point->floorplan_x,
                'y' => (float) $point->floorplan_y,
                'show_url' => null,
                'source' => 'map_point',
            ])
            ->values();

        $hazardMarkers = $hazardMarkers->concat($mapPointMarkers)->values();
        $floorplanMarkers = $floorplanMarkers->concat($floorplanMapPointMarkers)->values();

        $unmappedCount = PotentialHazardReport::query()
            ->where(fn ($query) => $query->whereNull('latitude')->orWhereNull('longitude'))
            ->where(fn ($query) => $query->whereNull('floorplan_x')->orWhereNull('floorplan_y'))
            ->count();

        $summary = [
            'total' => (clone $baseQuery)->count(),
            'inside' => (clone $baseQuery)->whereHas('location', fn (Builder $q) => $q->where('name', '!=', 'Diluar Polman'))->count(),
            'outside' => (clone $baseQuery)->whereHas('location', fn (Builder $q) => $q->where('name', 'Diluar Polman'))->count(),
        ];

        $locations = Location::query()->where('is_active', true)->orderBy('name')->get();
        $campusBuildingPolygons = app(PublicHazardMapData::class)->campusBuildingPolygons();

        return view('satgas.hazards.map', compact(
            'reports',
            'hazardMarkers',
            'floorplanMarkers',
            'summary',
            'filters',
            'locations',
            'campusBuildingPolygons',
            'unmappedCount'
        ));
    }

    public function exportMap(Request $request, HazardGisSpreadsheetExport $export): StreamedResponse
    {
        $filters = $this->gisFilters($request);
        $reports = $this->hazardGisQuery($filters)
            ->latest('submitted_at')
            ->get();

        return $export->download($reports, $filters);
    }

    protected function gisFilters(Request $request): array
    {
        return [
            'q' => trim((string) $request->string('q')),
            'hazard_type' => trim((string) $request->string('hazard_type')),
            'risk_level' => trim((string) $request->string('risk_level')),
            'status' => trim((string) $request->string('status')),
            'location_id' => trim((string) $request->string('location_id')),
            'scope' => trim((string) $request->string('scope')),
            'date_from' => trim((string) $request->string('date_from')),
            'date_to' => trim((string) $request->string('date_to')),
            'month' => trim((string) $request->string('month')),
            'year' => trim((string) $request->string('year')),
        ];
    }

    protected function hazardGisQuery(array $filters): Builder
    {
        return PotentialHazardReport::query()
            ->with(['location'])
            ->where(function (Builder $query) {
                $query
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude');
            })
            ->when($filters['q'] !== '', function (Builder $query) use ($filters) {
                $query->where(function (Builder $sub) use ($filters) {
                    $sub->where('report_number', 'like', '%' . $filters['q'] . '%')
                        ->orWhere('title', 'like', '%' . $filters['q'] . '%')
                        ->orWhere('reporter_name', 'like', '%' . $filters['q'] . '%')
                        ->orWhereHas('location', fn (Builder $loc) => $loc->where('name', 'like', '%' . $filters['q'] . '%'));
                });
            })
            ->when($filters['status'] !== '', fn (Builder $query) => $query->where('status', $filters['status']))
            ->when($filters['hazard_type'] !== '', fn (Builder $query) => $query->where('hazard_type', $filters['hazard_type']))
            ->when($filters['risk_level'] !== '', fn (Builder $query) => $query->where('risk_level', $filters['risk_level']))
            ->when($filters['location_id'] !== '', fn (Builder $query) => $query->where('location_id', $filters['location_id']))
            ->when($filters['scope'] === 'inside', function (Builder $query) {
                $query->whereHas('location', fn (Builder $loc) => $loc->where('name', '!=', 'Diluar Polman'));
            })
            ->when($filters['scope'] === 'outside', function (Builder $query) {
                $query->whereHas('location', fn (Builder $loc) => $loc->where('name', 'Diluar Polman'));
            })
            ->when($filters['date_from'] !== '', fn (Builder $query) => $query->whereDate('submitted_at', '>=', $filters['date_from']))
            ->when($filters['date_to'] !== '', fn (Builder $query) => $query->whereDate('submitted_at', '<=', $filters['date_to']))
            ->when($filters['month'] !== '', fn (Builder $query) => $query->whereMonth('submitted_at', $filters['month']))
            ->when($filters['year'] !== '', fn (Builder $query) => $query->whereYear('submitted_at', $filters['year']));
    }

    protected function hazardMarker(PotentialHazardReport $report): ?array
    {
        $latitude = $report->latitude;
        $longitude = $report->longitude;

        if ($latitude === null || $longitude === null) {
            return null;
        }

        $locationName = $report->location?->name ?? '-';

        return [
            'id' => $report->id,
            'report_number' => $report->report_number,
            'title' => $report->title,
            'reporter' => $report->reporter?->name ?? $report->reporter_name ?? '-',
            'location' => $locationName,
            'specific_location' => $report->specific_location ?? '-',
            'hazard_type' => $report->hazard_type ? str_replace('-', ' ', $report->hazard_type) : '-',
            'risk_level' => $report->risk_level ?: '-',
            'status' => $report->status,
            'submitted_at' => optional($report->submitted_at)->format('d M Y'),
            'latitude' => (float) $latitude,
            'longitude' => (float) $longitude,
            'scope' => $locationName === 'Diluar Polman' ? 'outside' : 'inside',
            'show_url' => route('satgas.hazards.show', $report),
        ];
    }

    public function show(PotentialHazardReport $potentialHazardReport): View
    {
        $potentialHazardReport->load([
            'reporter.role',
            'location',
            'attachments',
            'reviewer',
            'resolver',
            'mapper',
            'campusRoom',
        ]);

        $statusOptions = collect(
            $this->updatePotentialHazardReportStatus->allowedTransitions($potentialHazardReport->status)
        )->mapWithKeys(fn (string $status) => [$status => ucfirst($status)])->all();

        return view('satgas.hazards.show', [
            'hazardReport' => $potentialHazardReport,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function updateStatus(
        UpdatePotentialHazardStatusRequest $request,
        PotentialHazardReport $potentialHazardReport,
    ): RedirectResponse {
        $this->updatePotentialHazardReportStatus->handle(
            $potentialHazardReport,
            $request->string('status')->toString(),
            $request->string('response_note')->toString(),
            $request->user()->id,
        );

        return redirect()
            ->route('satgas.hazards.show', $potentialHazardReport)
            ->with('status', 'Status hazard report berhasil diperbarui.');
    }

    public function updatePinpoint(
        UpdateHazardPinpointRequest $request,
        PotentialHazardReport $potentialHazardReport,
    ): RedirectResponse {
        $potentialHazardReport->update([
            'map_source' => $request->string('map_source')->toString(),
            'latitude' => $request->filled('latitude') ? (float) $request->input('latitude') : $potentialHazardReport->latitude,
            'longitude' => $request->filled('longitude') ? (float) $request->input('longitude') : $potentialHazardReport->longitude,
            'floorplan_x' => $request->filled('floorplan_x') ? (float) $request->input('floorplan_x') : $potentialHazardReport->floorplan_x,
            'floorplan_y' => $request->filled('floorplan_y') ? (float) $request->input('floorplan_y') : $potentialHazardReport->floorplan_y,
            'risk_level' => $request->string('risk_level')->toString(),
            'mapped_by' => $request->user()->id,
            'mapped_at' => now(),
        ]);

        return redirect()
            ->route('satgas.hazards.show', $potentialHazardReport)
            ->with('status', 'Pinpoint GIS hazard berhasil disimpan.');
    }

    public function storeMapPoint(StoreHazardMapPointRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        HazardMapPoint::query()->create([
            ...$validated,
            'created_by' => $request->user()->id,
            'latitude' => $request->filled('latitude') ? (float) $request->input('latitude') : null,
            'longitude' => $request->filled('longitude') ? (float) $request->input('longitude') : null,
            'floorplan_x' => $request->filled('floorplan_x') ? (float) $request->input('floorplan_x') : null,
            'floorplan_y' => $request->filled('floorplan_y') ? (float) $request->input('floorplan_y') : null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('satgas.hazards.map')
            ->with('status', 'Titik area rawan GIS berhasil ditambahkan.');
    }
}
