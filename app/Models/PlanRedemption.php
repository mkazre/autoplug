<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanRedemption extends Model
{
    protected $fillable = [
        'plan_subscription_id', 'branch_id', 'booking_id', 'redemption_type',
        'description', 'items_json', 'amount_claimed', 'amount_approved',
        'status', 'reviewed_by', 'redeemed_at',
    ];

    protected $casts = [
        'plan_subscription_id' => 'integer',
        'branch_id' => 'integer',
        'booking_id' => 'integer',
        'items_json' => 'array',
        'amount_claimed' => 'decimal:2',
        'amount_approved' => 'decimal:2',
        'reviewed_by' => 'integer',
        'redeemed_at' => 'datetime',
    ];

    public function subscription(): BelongsTo { return $this->belongsTo(PlanSubscription::class, 'plan_subscription_id'); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function booking(): BelongsTo { return $this->belongsTo(Booking::class); }
}
