<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FloorplanRoom extends Model
{
    protected $fillable = [
        'floorplan_id',
        'campus_room_id',
        'shape_key',
        'shape_type',
        'geometry',
        'label',
        'default_fill_color',
        'incident_fill_color',
        'hazard_fill_color',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'geometry' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function floorplan(): BelongsTo
    {
        return $this->belongsTo(Floorplan::class);
    }

    public function campusRoom(): BelongsTo
    {
        return $this->belongsTo(CampusRoom::class);
    }
}
