<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GaragePhoto extends Model
{
    protected $fillable = ['garage_id', 'branch_id', 'photo_url', 'type'];

    protected $casts = [
        'garage_id' => 'integer',
        'branch_id' => 'integer',
    ];

    public function garage(): BelongsTo
    {
        return $this->belongsTo(Garage::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
