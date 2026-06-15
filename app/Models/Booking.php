<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = ['quote_id', 'plan_subscription_id', 'user_id', 'branch_id', 'scheduled_at', 'status', 'discount_amount', 'net_amount'];

    protected $casts = [
        'quote_id' => 'integer',
        'plan_subscription_id' => 'integer',
        'user_id' => 'integer',
        'branch_id' => 'integer',
        'scheduled_at' => 'datetime',
        'discount_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function planSubscription(): BelongsTo
    {
        return $this->belongsTo(PlanSubscription::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
