<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanBenefitItem extends Model
{
    protected $fillable = ['plan_product_id', 'item_name', 'coverage_limit', 'coverage_unit'];

    protected $casts = [
        'plan_product_id' => 'integer',
        'coverage_limit' => 'decimal:2',
    ];

    public function product(): BelongsTo { return $this->belongsTo(PlanProduct::class, 'plan_product_id'); }
}
