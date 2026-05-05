<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HazardMapPoint extends Model
{
    protected $fillable = [
        'created_by',
        'title',
        'hazard_type',
        'risk_level',
        'description',
        'map_source',
        'latitude',
        'longitude',
        'floorplan_x',
        'floorplan_y',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'floorplan_x' => 'decimal:3',
            'floorplan_y' => 'decimal:3',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
