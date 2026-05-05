<?php

namespace App\Http\Requests\Hazard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHazardMapPointRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSatgas() === true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'hazard_type' => ['nullable', Rule::in(['lingkungan', 'peralatan', 'listrik', 'zat-kimia'])],
            'risk_level' => ['required', Rule::in(['rendah', 'sedang', 'tinggi', 'kritis'])],
            'description' => ['nullable', 'string', 'max:2000'],
            'map_source' => ['required', Rule::in(['satellite', 'floorplan'])],
            'latitude' => ['nullable', 'required_if:map_source,satellite', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_if:map_source,satellite', 'numeric', 'between:-180,180'],
            'floorplan_x' => ['nullable', 'required_if:map_source,floorplan', 'numeric', 'between:0,4080'],
            'floorplan_y' => ['nullable', 'required_if:map_source,floorplan', 'numeric', 'between:0,3060'],
        ];
    }
}
