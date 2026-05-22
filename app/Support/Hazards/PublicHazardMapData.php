<?php

namespace App\Support\Hazards;

use App\Models\HazardMapPoint;
use App\Models\IncidentReport;
use App\Models\PotentialHazardReport;

class PublicHazardMapData
{
    public function build(): array
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
                'building_key' => 'gedung-teori',
                'floor' => 2,
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
                'building_key' => 'gedung-teori',
                'floor' => 2,
                'x' => (float) $point->floorplan_x,
                'y' => (float) $point->floorplan_y,
            ])
            ->values();

        $hazardMarkers = $hazardMarkers->concat($mapPointMarkers)->values();
        $floorplanMarkers = $floorplanMarkers->concat($floorplanMapPointMarkers)->values();
        $incidentMarkers = $this->incidentMarkers();

        return [
            'hazardMarkers' => $hazardMarkers,
            'incidentMarkers' => $incidentMarkers,
            'floorplanMarkers' => $floorplanMarkers,
            'summaryCounts' => [
                'total' => $hazardMarkers->count() + $floorplanMarkers->count(),
                'tinggi' => $hazardMarkers->whereIn('risk_level', ['tinggi', 'kritis'])->count()
                    + $floorplanMarkers->whereIn('risk_level', ['tinggi', 'kritis'])->count(),
                'aktif' => $hazardMarkers->where('status', '!=', 'resolved')->count()
                    + $floorplanMarkers->where('status', '!=', 'resolved')->count(),
            ],
            'campusBuildingPolygons' => $this->campusBuildingPolygons(),
            'campusBoundaryPolygon' => $this->campusBoundaryPolygon(),
        ];
    }

    protected function incidentMarkers()
    {
        $buildingPolygons = collect($this->campusBuildingPolygons())->keyBy('key');

        return IncidentReport::query()
            ->with(['category', 'location', 'verifiedLocation', 'campusRoom'])
            ->where(function ($query) {
                $query
                    ->where(function ($subQuery) {
                        $subQuery->whereNotNull('verified_latitude')->whereNotNull('verified_longitude');
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereNotNull('latitude')->whereNotNull('longitude');
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereNotNull('campus_room_id')->whereNotNull('building_key');
                    });
            })
            ->latest('incident_date')
            ->limit(500)
            ->get()
            ->map(function (IncidentReport $report) use ($buildingPolygons) {
                $latitude = $report->verified_latitude ?? $report->latitude;
                $longitude = $report->verified_longitude ?? $report->longitude;
                $buildingKey = $report->building_key ?? $report->campusRoom?->building_key;
                $floor = $report->building_floor ?? $report->campusRoom?->floor;
                $roomName = $report->campusRoom?->name;
                $mapSource = 'gps';

                if (($latitude === null || $longitude === null) && $buildingKey) {
                    $center = $this->buildingCenter($buildingPolygons->get($buildingKey)['coordinates'] ?? []);
                    $latitude = $center[0] ?? null;
                    $longitude = $center[1] ?? null;
                    $mapSource = 'building_room';
                }

                if ($latitude === null || $longitude === null) {
                    return null;
                }

                $locationName = $report->verifiedLocation?->name ?? $report->location?->name ?? '-';
                $specificLocation = $report->verified_specific_location ?? $report->specific_location ?? '-';

                if ($roomName) {
                    $specificLocation = trim(($floor ? 'Lantai ' . $floor . ' - ' : '') . $roomName);
                }

                return [
                    'id' => $report->id,
                    'report_number' => $report->report_number,
                    'title' => $report->title,
                    'location' => $locationName,
                    'specific_location' => $specificLocation,
                    'category' => $report->category?->name ?? '-',
                    'severity_level' => $report->severity_level ?: 'medium',
                    'status' => $report->status,
                    'incident_date' => optional($report->incident_date)->format('d M Y'),
                    'latitude' => (float) $latitude,
                    'longitude' => (float) $longitude,
                    'building_key' => $buildingKey,
                    'floor' => $floor,
                    'campus_room_id' => $report->campus_room_id,
                    'map_source' => $mapSource,
                    'scope' => $locationName === 'Diluar Polman' ? 'outside' : 'inside',
                ];
            })
            ->filter()
            ->values();
    }

    protected function buildingCenter(array $coordinates): ?array
    {
        if ($coordinates === []) {
            return null;
        }

        $points = collect($coordinates);

        return [
            $points->avg(fn (array $point) => (float) $point[0]),
            $points->avg(fn (array $point) => (float) $point[1]),
        ];
    }

    public function campusBuildingPolygons(): array
    {
        return [
            [
                'key' => 'gedung-teori',
                'name' => 'Gedung Teori & Kantor',
                'color' => '#2563eb',
                'default_floor' => 2,
                'floors' => [2, 3],
                'coordinates' => [
                    [-6.87732, 107.62028],
                    [-6.87733, 107.62114],
                    [-6.87753, 107.62114],
                    [-6.87751, 107.62029],
                ],
            ],
            [
                'key' => 'gedung-kantor',
                'name' => 'Gedung Kantor',
                'color' => '#dc2626',
                'coordinates' => [
                    [-6.87717, 107.61987],
                    [-6.87717, 107.62008],
                    [-6.87733, 107.62007],
                    [-6.87734, 107.62012],
                    [-6.87752, 107.62011],
                    [-6.87753, 107.61984],
                ],
            ],
            [
                'key' => 'gedung-mekanik',
                'name' => 'Gedung Mekanik',
                'color' => '#16a34a',
                'coordinates' => [
                    [-6.87702, 107.62026],
                    [-6.87704, 107.62118],
                    [-6.87728, 107.62117],
                    [-6.87726, 107.62024],
                ],
            ],
            [
                'key' => 'gedung-fe',
                'name' => 'Gedung FE',
                'color' => '#9333ea',
                'coordinates' => [
                    [-6.87639, 107.62084],
                    [-6.87639, 107.62127],
                    [-6.87694, 107.62127],
                    [-6.87694, 107.62084],
                ],
            ],
            [
                'key' => 'gedung-grc',
                'name' => 'Gedung GRC',
                'color' => '#f59e0b',
                'coordinates' => [
                    [-6.87675, 107.62039],
                    [-6.87676, 107.62078],
                    [-6.87693, 107.62077],
                    [-6.87693, 107.62038],
                ],
            ],
        ];
    }

    public function campusBoundaryPolygon(): array
    {
        return [
            [-6.87715887735517, 107.61984870172961],
            [-6.87776142492383, 107.61981835600203],
            [-6.877674808757875, 107.62141909313236],
            [-6.878032571080371, 107.62151392353108],
            [-6.878484174399816, 107.62131657771924],
            [-6.8788463291320125, 107.62168135810514],
            [-6.878484174399816, 107.62201931640384],
            [-6.878036806408215, 107.6221373335875],
            [-6.877024901063348, 107.62203540965614],
            [-6.876455037630919, 107.6218530194632],
            [-6.875885173514988, 107.62182083295856],
            [-6.875879847678909, 107.6215150611645],
            [-6.876401779331286, 107.621203924953],
            [-6.876375150179223, 107.62074794947064],
            [-6.876391127670642, 107.6202436942313],
            [-6.876380476009756, 107.62023296539644],
            [-6.876790564781358, 107.62022760097899],
            [-6.876827845561228, 107.6201042193779],
            [-6.8768225197357085, 107.62013104146509],
            [-6.877152720804798, 107.62017932122203],
            [-6.877158046626614, 107.61985209175822],
            [-6.877152720804798, 107.61984672734077],
        ];
    }
}
