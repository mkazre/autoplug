<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanSignature extends Model
{
    protected $fillable = [
        'plan_application_id', 'signatory_name', 'signature_path',
        'signed_at', 'ip_address', 'device_info', 'terms_version',
    ];

    protected $casts = ['plan_application_id' => 'integer', 'signed_at' => 'datetime'];

    public function application(): BelongsTo { return $this->belongsTo(PlanApplication::class, 'plan_application_id'); }
}
