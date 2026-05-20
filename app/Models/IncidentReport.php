<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidentReport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'report_number',
        'reported_by',
        'reporter_name',
        'reporter_email',
        'reporter_whatsapp',
        'victim_user_id',
        'victim_name',
        'victim_address',
        'victim_position',
        'victim_position_description',
        'victim_gender',
        'victim_age',
        'incident_category_id',
        'injury_category_id',
        'body_part_id',
        'location_id',
        'incident_date',
        'incident_time',
        'witness_name',
        'ppe_used',
        'severity_level',
        'title',
        'chronology',
        'cause',
        'initial_action',
        'impact',
        'unsafe_conditions',
        'unsafe_actions',
        'unsafe_condition_cause',
        'unsafe_action_cause',
        'warning_given_before_incident',
        'incident_previously_occurred',
        'proposed_preventions',
        'prevention_action_plan',
        'status',
        'assigned_satgas_id',
        'verified_by',
        'closed_by',
        'submitted_at',
        'verified_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'incident_date' => 'date',
            'unsafe_conditions' => 'array',
            'unsafe_actions' => 'array',
            'warning_given_before_incident' => 'boolean',
            'incident_previously_occurred' => 'boolean',
            'proposed_preventions' => 'array',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function victim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'victim_user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(IncidentCategory::class, 'incident_category_id');
    }

    public function injuryCategory(): BelongsTo
    {
        return $this->belongsTo(InjuryCategory::class);
    }

    public function bodyPart(): BelongsTo
    {
        return $this->belongsTo(BodyPart::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function assignedSatgas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_satgas_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(IncidentAttachment::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(IncidentStatusHistory::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(IncidentFollowUp::class);
    }
}
