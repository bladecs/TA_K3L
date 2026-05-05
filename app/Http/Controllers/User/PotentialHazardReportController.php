<?php

namespace App\Http\Controllers\User;

use App\Actions\Hazards\CreatePotentialHazardReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hazard\StorePotentialHazardReportRequest;
use App\Models\HazardMapPoint;
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
        return view('user.hazards.create', $this->reportFormOptions->hazard());
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
                'mapped_at' => optional($report->mapped_at)->format('d M Y H:i'),
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
                'report_number' => 'GIS-' . str_pad((string) $point->id, 4, '0', STR_PAD_LEFT),
                'title' => $point->title,
                'location' => 'Titik GIS Satgas',
                'specific_location' => $point->description ?: '-',
                'hazard_type' => $point->hazard_type ? str_replace('-', ' ', $point->hazard_type) : '-',
                'risk_level' => $point->risk_level,
                'status' => 'active',
                'latitude' => (float) $point->latitude,
                'longitude' => (float) $point->longitude,
                'mapped_at' => optional($point->created_at)->format('d M Y H:i'),
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
                'report_number' => 'GIS-' . str_pad((string) $point->id, 4, '0', STR_PAD_LEFT),
                'title' => $point->title,
                'location' => 'Titik GIS Satgas',
                'specific_location' => $point->description ?: '-',
                'risk_level' => $point->risk_level,
                'status' => 'active',
                'x' => (float) $point->floorplan_x,
                'y' => (float) $point->floorplan_y,
            ])
            ->values();

        $hazardMarkers = $hazardMarkers->concat($mapPointMarkers)->values();
        $floorplanMarkers = $floorplanMarkers->concat($floorplanMapPointMarkers)->values();

        $summaryCounts = [
            'total' => $hazardMarkers->count() + $floorplanMarkers->count(),
            'tinggi' => $hazardMarkers->whereIn('risk_level', ['tinggi', 'kritis'])->count()
                + $floorplanMarkers->whereIn('risk_level', ['tinggi', 'kritis'])->count(),
            'aktif' => $hazardMarkers->where('status', '!=', 'resolved')->count()
                + $floorplanMarkers->where('status', '!=', 'resolved')->count(),
        ];

        return view('user.hazards.map', compact('hazardMarkers', 'floorplanMarkers', 'summaryCounts'));
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
