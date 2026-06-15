<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanProduct extends Model
{
    protected $fillable = [
        'name', 'type', 'description', 'min_km_excl', 'max_km', 'max_age_years',
        'requires_full_history', 'base_price', 'terms_version', 'terms_doc_path', 'is_active',
    ];

    protected $casts = [
        'min_km_excl' => 'integer',
        'max_km' => 'integer',
        'max_age_years' => 'integer',
        'requires_full_history' => 'boolean',
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function pricingTiers(): HasMany { return $this->hasMany(PlanPricingTier::class); }
    public function benefitItems(): HasMany { return $this->hasMany(PlanBenefitItem::class); }
    public function discounts(): HasMany { return $this->hasMany(PlanHolderDiscount::class); }
    public function applications(): HasMany { return $this->hasMany(PlanApplication::class); }
    public function subscriptions(): HasMany { return $this->hasMany(PlanSubscription::class); }
}
