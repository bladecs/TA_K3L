<?php

namespace App\Support\Reports;

use App\Models\BodyPart;
use App\Models\IncidentCategory;
use App\Models\InjuryCategory;
use App\Models\Location;
use Illuminate\Support\Facades\Schema;

class ReportFormOptions
{
    public function incident(): array
    {
        return [
            'categories' => IncidentCategory::query()->orderBy('name')->get(),
            'locations' => $this->locations(),
            'injuryCategories' => InjuryCategory::query()->orderBy('name')->get(),
            'bodyParts' => BodyPart::query()->orderBy('name')->get(),
            'severityOptions' => [
                'low' => 'Rendah',
                'medium' => 'Sedang',
                'high' => 'Tinggi',
                'critical' => 'Kritis',
            ],
        ];
    }

    public function hazard(): array
    {
        $hazardTypes = [
            ['key' => 'lingkungan', 'label' => 'Lingkungan', 'icon' => 'eco'],
            ['key' => 'peralatan', 'label' => 'Peralatan', 'icon' => 'construction'],
            ['key' => 'listrik', 'label' => 'Listrik', 'icon' => 'bolt'],
            ['key' => 'zat-kimia', 'label' => 'Zat Kimia', 'icon' => 'science'],
        ];

        return [
            'locations' => $this->locations(),
            'hazardTypes' => $hazardTypes,
            'selectedHazardType' => old('hazard_type', $hazardTypes[0]['key']),
        ];
    }

    protected function locations()
    {
        $locations = collect();

        if (Schema::hasTable('locations')) {
            $locations = Location::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        if ($locations->isEmpty()) {
            $locations = collect([
                (object) ['id' => 1, 'name' => 'Bengkel Manufaktur'],
                (object) ['id' => 2, 'name' => 'Laboratorium Elektronika'],
                (object) ['id' => 3, 'name' => 'Workshop Material'],
            ]);
        }

        return $locations;
    }
}
