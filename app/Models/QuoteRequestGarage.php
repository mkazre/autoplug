<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class QuoteRequestGarage extends Model
{
    protected $fillable = ['quote_request_id', 'branch_id', 'status', 'expires_at'];

    protected $casts = [
        'quote_request_id' => 'integer',
        'branch_id' => 'integer',
        'expires_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function quote(): HasOne
    {
        return $this->hasOne(Quote::class);
    }
}
