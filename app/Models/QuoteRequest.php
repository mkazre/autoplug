<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteRequest extends Model
{
    protected $fillable = [
        'user_id', 'vehicle_id', 'service_id', 'description',
        'lat', 'lng', 'radius_km', 'status',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'vehicle_id' => 'integer',
        'service_id' => 'integer',
        'radius_km' => 'integer',
    ];

    /**
     * Derived status for display: open | accepted | expired | cancelled.
     * "open" while any garage can still quote, or any submitted quote is still live.
     * Relies on requestGarages (with their quote) being loaded.
     */
    public function displayStatus(): string
    {
        if ($this->status === 'cancelled') {
            return 'cancelled';
        }
        if ($this->status === 'closed') {
            return 'accepted';
        }

        foreach ($this->requestGarages as $rg) {
            if ($rg->status === 'pending' && ! $rg->isExpired()) {
                return 'open';
            }
            if ($rg->quote && $rg->quote->status === 'pending' && ! $rg->quote->isExpired()) {
                return 'open';
            }
        }

        return 'expired';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function requestGarages(): HasMany
    {
        return $this->hasMany(QuoteRequestGarage::class);
    }
}
