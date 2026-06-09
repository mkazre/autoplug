<?php

namespace App\Models;

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
}
