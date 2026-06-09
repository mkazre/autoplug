<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'garage_id', 'name', 'address', 'lat', 'lng', 'phone', 'is_active',
    ];

    protected $casts = [
        'garage_id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function garage(): BelongsTo
    {
        return $this->belongsTo(Garage::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(GarageService::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(GaragePhoto::class);
    }

    /**
     * Filter branches within $radiusKm of a point, adding a `distance` column (km), nearest first.
     */
    public function scopeWithinRadius(Builder $query, float $lat, float $lng, float $radiusKm): Builder
    {
        $haversine = '(6371 * acos(least(1, cos(radians(?)) * cos(radians(branches.lat)) * cos(radians(branches.lng) - radians(?)) + sin(radians(?)) * sin(radians(branches.lat)))))';

        return $query
            ->whereNotNull('branches.lat')
            ->whereNotNull('branches.lng')
            ->select('branches.*')
            ->selectRaw($haversine.' AS distance', [$lat, $lng, $lat])
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance');
    }
}
