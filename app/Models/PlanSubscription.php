<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanSubscription extends Model
{
    protected $fillable = [
        'plan_application_id', 'user_id', 'vehicle_id', 'plan_product_id', 'pricing_tier_id',
        'start_date', 'end_date', 'status', 'km_at_start', 'current_balance', 'total_paid',
        'missed_count', 'contract_path', 'referral_code',
    ];

    protected $casts = [
        'plan_application_id' => 'integer',
        'user_id' => 'integer',
        'vehicle_id' => 'integer',
        'plan_product_id' => 'integer',
        'pricing_tier_id' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'km_at_start' => 'integer',
        'current_balance' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'missed_count' => 'integer',
    ];

    public function application(): BelongsTo { return $this->belongsTo(PlanApplication::class, 'plan_application_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function vehicle(): BelongsTo { return $this->belongsTo(Vehicle::class); }
    public function product(): BelongsTo { return $this->belongsTo(PlanProduct::class, 'plan_product_id'); }
    public function tier(): BelongsTo { return $this->belongsTo(PlanPricingTier::class, 'pricing_tier_id'); }
    public function installments(): HasMany { return $this->hasMany(PlanInstallment::class); }
    public function payments(): HasMany { return $this->hasMany(PlanPayment::class); }
    public function redemptions(): HasMany { return $this->hasMany(PlanRedemption::class); }
}
