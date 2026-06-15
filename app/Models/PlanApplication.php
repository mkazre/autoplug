<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PlanApplication extends Model
{
    protected $fillable = [
        'user_id', 'vehicle_id', 'plan_product_id', 'pricing_tier_id', 'status',
        'terms_version', 'payment_method', 'referral_code', 'reject_reason',
        'submitted_at', 'approved_at', 'approved_by',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'vehicle_id' => 'integer',
        'plan_product_id' => 'integer',
        'pricing_tier_id' => 'integer',
        'approved_by' => 'integer',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function vehicle(): BelongsTo { return $this->belongsTo(Vehicle::class); }
    public function product(): BelongsTo { return $this->belongsTo(PlanProduct::class, 'plan_product_id'); }
    public function tier(): BelongsTo { return $this->belongsTo(PlanPricingTier::class, 'pricing_tier_id'); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function documents(): HasMany { return $this->hasMany(PlanApplicationDocument::class); }
    public function signature(): HasOne { return $this->hasOne(PlanSignature::class); }
    public function subscription(): HasOne { return $this->hasOne(PlanSubscription::class); }
}
