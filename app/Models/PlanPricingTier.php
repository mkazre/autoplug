<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanPricingTier extends Model
{
    protected $fillable = [
        'plan_product_id', 'vehicle_category', 'term_months',
        'installment_count', 'monthly_price', 'upfront_price',
    ];

    protected $casts = [
        'plan_product_id' => 'integer',
        'term_months' => 'integer',
        'installment_count' => 'integer',
        'monthly_price' => 'decimal:2',
        'upfront_price' => 'decimal:2',
    ];

    public function product(): BelongsTo { return $this->belongsTo(PlanProduct::class, 'plan_product_id'); }
}
