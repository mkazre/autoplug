<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanPayment extends Model
{
    protected $fillable = [
        'plan_subscription_id', 'plan_installment_id', 'gateway', 'method',
        'amount', 'reference', 'txn_reference', 'paid_on', 'proof_path',
        'status', 'paid_at', 'verified_by', 'verified_at',
    ];

    protected $casts = [
        'plan_subscription_id' => 'integer',
        'plan_installment_id' => 'integer',
        'amount' => 'decimal:2',
        'paid_on' => 'date',
        'paid_at' => 'datetime',
        'verified_by' => 'integer',
        'verified_at' => 'datetime',
    ];

    public function subscription(): BelongsTo { return $this->belongsTo(PlanSubscription::class, 'plan_subscription_id'); }
    public function installment(): BelongsTo { return $this->belongsTo(PlanInstallment::class, 'plan_installment_id'); }
}
