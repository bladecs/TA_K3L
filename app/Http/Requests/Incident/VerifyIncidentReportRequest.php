<?php

namespace App\Http\Requests\Incident;

use App\Models\IncidentReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyIncidentReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var IncidentReport|null $report */
        $report = $this->route('incidentReport');

        return $report !== null && $this->user()?->can('verify', $report) === true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:200'],
            'incident_category_id' => ['nullable', 'integer', 'exists:incident_categories,id'],
            'severity_level' => ['nullable', Rule::in(['low', 'medium', 'high', 'critical'])],
            'injury_category_id' => ['nullable', 'integer', 'exists:injury_categories,id'],
            'body_part_id' => ['nullable', 'integer', 'exists:body_parts,id'],
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
            'verified_location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'verified_specific_location' => ['nullable', 'string', 'max:255'],
            'verified_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'verified_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'verified_location_accuracy' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'verification_note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'unsafe_conditions' => $this->input('unsafe_conditions', []),
            'unsafe_actions' => $this->input('unsafe_actions', []),
            'proposed_preventions' => $this->input('proposed_preventions', []),
        ]);
    }
}
