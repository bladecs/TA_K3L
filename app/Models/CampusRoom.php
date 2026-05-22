<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampusRoom extends Model
{
    protected $fillable = [
        'building_key',
        'building_name',
        'floor',
        'name',
        'code',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'floor' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function floorplanRooms(): HasMany
    {
        return $this->hasMany(FloorplanRoom::class);
    }
}
