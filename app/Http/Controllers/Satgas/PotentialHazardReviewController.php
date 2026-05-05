<?php

namespace App\Http\Controllers\Satgas;

use App\Actions\Hazards\UpdatePotentialHazardReportStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hazard\StoreHazardMapPointRequest;
use App\Http\Requests\Hazard\UpdateHazardPinpointRequest;
use App\Http\Requests\Hazard\UpdatePotentialHazardStatusRequest;
use App\Models\HazardMapPoint;
use App\Models\PotentialHazardReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

    public function map(): View
    {
        $hazardMarkers = PotentialHazardReport::query()
            ->with(['location'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest('mapped_at')
            ->get()
            ->map(fn (PotentialHazardReport $report) => [
                'id' => $report->id,
                'report_number' => $report->report_number,
                'title' => $report->title,
                'location' => $report->location?->name ?? '-',
                'specific_location' => $report->specific_location ?: '-',
                'hazard_type' => str_replace('-', ' ', $report->hazard_type),
                'risk_level' => $report->risk_level ?: 'sedang',
                'status' => $report->status,
                'latitude' => (float) $report->latitude,
                'longitude' => (float) $report->longitude,
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
                'x' => (float) $report->floorplan_x,
                'y' => (float) $report->floorplan_y,
                'show_url' => route('satgas.hazards.show', $report),
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
                'x' => (float) $point->floorplan_x,
                'y' => (float) $point->floorplan_y,
                'show_url' => null,
                'source' => 'map_point',
            ])
            ->values();

        $unmappedCount = PotentialHazardReport::query()
            ->where(fn ($query) => $query->whereNull('latitude')->orWhereNull('longitude'))
            ->where(fn ($query) => $query->whereNull('floorplan_x')->orWhereNull('floorplan_y'))
            ->count();

        $hazardMarkers = $hazardMarkers->concat($mapPointMarkers)->values();
        $floorplanMarkers = $floorplanMarkers->concat($floorplanMapPointMarkers)->values();

        return view('satgas.hazards.map', compact('hazardMarkers', 'floorplanMarkers', 'unmappedCount'));
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
