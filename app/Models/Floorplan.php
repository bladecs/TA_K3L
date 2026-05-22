<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Floorplan extends Model
{
    protected $fillable = [
        'location_id',
        'building_key',
        'building_name',
        'floor',
        'name',
        'version',
        'source_type',
        'file_disk',
        'file_path',
        'original_filename',
        'svg_markup',
        'canvas_width',
        'canvas_height',
        'metadata',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'floor' => 'integer',
            'version' => 'integer',
            'canvas_width' => 'integer',
            'canvas_height' => 'integer',
            'metadata' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(FloorplanRoom::class);
    }
}
