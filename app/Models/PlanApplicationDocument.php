<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanApplicationDocument extends Model
{
    protected $fillable = ['plan_application_id', 'document_type', 'file_path', 'uploaded_at'];

    protected $casts = ['plan_application_id' => 'integer', 'uploaded_at' => 'datetime'];

    public function application(): BelongsTo { return $this->belongsTo(PlanApplication::class, 'plan_application_id'); }
}
