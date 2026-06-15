<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GaragePayout extends Model
{
    protected $fillable = ['garage_id', 'type', 'source_id', 'amount', 'status', 'cycle', 'paid_at'];

    protected $casts = [
        'garage_id' => 'integer',
        'source_id' => 'integer',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function garage(): BelongsTo { return $this->belongsTo(Garage::class); }
}
