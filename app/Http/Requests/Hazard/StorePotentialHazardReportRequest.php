<?php

namespace App\Http\Requests\Hazard;

use App\Support\Hazards\PublicHazardMapData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePotentialHazardReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'reporter_name' => ['required', 'string', 'max:150'],
            'reporter_email' => ['required', 'email:rfc', 'max:150'],
            'reporter_whatsapp' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'hazard_type' => ['required', Rule::in(['lingkungan', 'peralatan', 'listrik', 'zat-kimia'])],
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            'specific_location' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'location_accuracy' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'building_key' => ['nullable', 'string', 'max:100'],
            'building_floor' => ['nullable', 'integer', 'min:1', 'max:99'],
            'campus_room_id' => ['nullable', 'integer', 'exists:campus_rooms,id'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'attachments' => ['nullable', 'array', 'max:3'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'hazard_type.in' => 'Jenis temuan yang dipilih tidak valid.',
            'location_id.exists' => 'Lokasi yang dipilih tidak valid.',
            'specific_location.required' => 'Detail lokasi hazard wajib diisi.',
            'latitude.required' => 'Koordinat GPS hazard wajib diambil.',
            'longitude.required' => 'Koordinat GPS hazard wajib diambil.',
            'reporter_whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, +, -, dan tanda kurung.',
            'attachments.max' => 'Jumlah lampiran maksimal 3 file.',
            'attachments.*.mimes' => 'Lampiran hanya boleh berupa JPG, JPEG, atau PNG.',
            'attachments.*.max' => 'Ukuran tiap lampiran maksimal 5 MB.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->has('latitude') || $validator->errors()->has('longitude')) {
                return;
            }

            $latitude = (float) $this->input('latitude');
            $longitude = (float) $this->input('longitude');
            $boundary = app(PublicHazardMapData::class)->campusBoundaryPolygon();

            if (! $this->isPointInsidePolygon([$latitude, $longitude], $boundary)) {
                $validator->errors()->add('latitude', 'Koordinat hazard harus berada di area Polman.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        if (! $this->user()) {
            return;
        }

        $phone = $this->user()->phone;
        $whatsapp = $this->input('reporter_whatsapp') ?: (
            $phone && preg_match('/^[0-9+\-\s()]+$/', $phone) ? $phone : '0'
        );

        $this->merge([
            'reporter_name' => $this->input('reporter_name') ?: $this->user()->name,
            'reporter_email' => $this->input('reporter_email') ?: $this->user()->email,
            'reporter_whatsapp' => $whatsapp,
        ]);
    }

    protected function isPointInsidePolygon(array $point, array $polygon): bool
    {
        [$lat, $lng] = $point;
        $inside = false;

        for ($i = 0, $j = count($polygon) - 1; $i < count($polygon); $j = $i++) {
            [$latI, $lngI] = $polygon[$i];
            [$latJ, $lngJ] = $polygon[$j];
            $intersects = (($lngI > $lng) !== ($lngJ > $lng))
                && ($lat < (($latJ - $latI) * ($lng - $lngI)) / ($lngJ - $lngI) + $latI);

            if ($intersects) {
                $inside = ! $inside;
            }
        }

        return $inside;
    }
}
