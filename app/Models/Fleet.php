<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fleet extends Model
{
    protected $fillable = ['owner_id', 'name'];

    protected $casts = ['owner_id' => 'integer'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(User::class, 'fleet_id');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'fleet_id');
    }
}
