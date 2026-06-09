<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quote extends Model
{
    protected $fillable = [
        'quote_request_garage_id', 'items_json', 'total_price',
        'notes', 'valid_until', 'status',
    ];

    protected $casts = [
        'quote_request_garage_id' => 'integer',
        'items_json' => 'array',
        'valid_until' => 'date',
        'total_price' => 'decimal:2',
    ];

    public function quoteRequestGarage(): BelongsTo
    {
        return $this->belongsTo(QuoteRequestGarage::class);
    }

    public function booking(): HasOne
    {
        return $this->hasOne(Booking::class)->latest('id');
    }

    public function activeBooking(): HasOne
    {
        return $this->hasOne(Booking::class)->where('status', '!=', 'cancelled')->latest('id');
    }
}
