<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanDiscountUsage extends Model
{
    protected $fillable = [
        'plan_subscription_id', 'user_id', 'booking_id', 'quote_id', 'garage_id',
        'original_amount', 'discount_amount', 'net_amount',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];
}
