<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanPayment extends Model
{
    protected $fillable = [
        'plan_subscription_id', 'plan_installment_id', 'gateway',
        'amount', 'reference', 'status', 'paid_at',
    ];

    protected $casts = [
        'plan_subscription_id' => 'integer',
        'plan_installment_id' => 'integer',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function subscription(): BelongsTo { return $this->belongsTo(PlanSubscription::class, 'plan_subscription_id'); }
    public function installment(): BelongsTo { return $this->belongsTo(PlanInstallment::class, 'plan_installment_id'); }
}
