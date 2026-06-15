<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanInstallment extends Model
{
    protected $fillable = [
        'plan_subscription_id', 'installment_number', 'amount_due',
        'due_date', 'status', 'paid_at', 'plan_payment_id',
    ];

    protected $casts = [
        'plan_subscription_id' => 'integer',
        'installment_number' => 'integer',
        'amount_due' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'plan_payment_id' => 'integer',
    ];

    public function subscription(): BelongsTo { return $this->belongsTo(PlanSubscription::class, 'plan_subscription_id'); }
}
