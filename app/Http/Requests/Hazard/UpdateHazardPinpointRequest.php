<?php

namespace App\Http\Requests\Hazard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHazardPinpointRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSatgas() === true;
    }

    public function rules(): array
    {
        return [
            'map_source' => ['required', Rule::in(['satellite', 'floorplan'])],
            'latitude' => ['nullable', 'required_if:map_source,satellite', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_if:map_source,satellite', 'numeric', 'between:-180,180'],
            'floorplan_x' => ['nullable', 'required_if:map_source,floorplan', 'numeric', 'between:0,4080'],
            'floorplan_y' => ['nullable', 'required_if:map_source,floorplan', 'numeric', 'between:0,3060'],
            'risk_level' => ['required', Rule::in(['rendah', 'sedang', 'tinggi', 'kritis'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'map_source' => 'sumber peta',
            'floorplan_x' => 'posisi X denah',
            'floorplan_y' => 'posisi Y denah',
            'risk_level' => 'level risiko',
        ];
    }
}
