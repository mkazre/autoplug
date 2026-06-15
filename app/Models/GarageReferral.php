<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GarageReferral extends Model
{
    protected $fillable = [
        'referring_garage_id', 'referred_user_id', 'plan_subscription_id',
        'reward_status', 'reward_amount', 'paid_at',
    ];

    protected $casts = [
        'referring_garage_id' => 'integer',
        'referred_user_id' => 'integer',
        'plan_subscription_id' => 'integer',
        'reward_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function garage(): BelongsTo { return $this->belongsTo(Garage::class, 'referring_garage_id'); }
    public function referredUser(): BelongsTo { return $this->belongsTo(User::class, 'referred_user_id'); }
    public function subscription(): BelongsTo { return $this->belongsTo(PlanSubscription::class, 'plan_subscription_id'); }
}
