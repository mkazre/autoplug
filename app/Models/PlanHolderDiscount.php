<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanHolderDiscount extends Model
{
    protected $fillable = ['plan_product_id', 'discount_type', 'discount_value', 'applies_to', 'is_active'];

    protected $casts = [
        'plan_product_id' => 'integer',
        'discount_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo { return $this->belongsTo(PlanProduct::class, 'plan_product_id'); }
}
