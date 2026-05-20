<?php

namespace App\Http\Requests\Incident;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIncidentReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'reporter_name' => ['nullable', 'string', 'max:150'],
            'reporter_email' => ['nullable', 'email:rfc', 'max:150'],
            'reporter_whatsapp' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'incident_category_id' => ['nullable', 'integer', 'exists:incident_categories,id'],
            'injury_category_id' => ['nullable', 'integer', 'exists:injury_categories,id'],
            'body_part_id' => ['nullable', 'integer', 'exists:body_parts,id'],
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            'incident_date' => ['required', 'date', 'before_or_equal:today'],
            'incident_time' => ['nullable', 'date_format:H:i'],
            'severity_level' => ['nullable', Rule::in(['low', 'medium', 'high', 'critical'])],
            'victim_type' => ['nullable', Rule::in(['self', 'other'])],
            'victim_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'victim_name' => ['nullable', 'string', 'max:150'],
            'victim_address' => ['nullable', 'string', 'max:2000'],
            'victim_position' => ['nullable', Rule::in(['mahasiswa', 'karyawan', 'publik', 'kontraktor', 'pengunjung'])],
            'victim_position_description' => ['nullable', 'string', 'max:200'],
            'victim_gender' => ['nullable', Rule::in(['male', 'female'])],
            'victim_age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'witness_name' => ['nullable', 'string', 'max:150'],
            'ppe_used' => ['nullable', 'string', 'max:2000'],
            'chronology' => ['required', 'string', 'min:30'],
            'cause' => ['nullable', 'string', 'max:5000'],
            'initial_action' => ['nullable', 'string', 'max:5000'],
            'impact' => ['nullable', 'string', 'max:5000'],
            'unsafe_conditions' => ['nullable', 'array'],
            'unsafe_conditions.*' => ['string', 'max:100'],
            'unsafe_actions' => ['nullable', 'array'],
            'unsafe_actions.*' => ['string', 'max:100'],
            'unsafe_condition_cause' => ['nullable', 'string', 'max:5000'],
            'unsafe_action_cause' => ['nullable', 'string', 'max:5000'],
            'warning_given_before_incident' => ['nullable', 'boolean'],
            'incident_previously_occurred' => ['nullable', 'boolean'],
            'proposed_preventions' => ['nullable', 'array'],
            'proposed_preventions.*' => ['string', 'max:100'],
            'prevention_action_plan' => ['nullable', 'string', 'max:5000'],
            'attachments' => ['nullable', 'array', 'max:3'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'incident_category_id.exists' => 'Kategori insiden yang dipilih tidak valid.',
            'injury_category_id.exists' => 'Jenis cedera yang dipilih tidak valid.',
            'body_part_id.exists' => 'Bagian tubuh yang dipilih tidak valid.',
            'location_id.exists' => 'Lokasi kejadian yang dipilih tidak valid.',
            'incident_date.before_or_equal' => 'Tanggal kejadian tidak boleh melebihi hari ini.',
            'incident_time.date_format' => 'Format waktu kejadian harus jam dan menit.',
            'reporter_whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, +, -, dan tanda kurung.',
            'chronology.min' => 'Kronologi minimal 30 karakter agar laporan cukup informatif.',
            'attachments.*.max' => 'Ukuran tiap lampiran maksimal 5 MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->user()) {
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

        if ($this->user() && $this->input('victim_type') === 'self') {
            $this->merge([
                'victim_user_id' => $this->user()?->id,
            ]);
        }
    }
}
