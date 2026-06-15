<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanAuditLog extends Model
{
    protected $fillable = ['subject_type', 'subject_id', 'actor_id', 'action', 'changes_json'];

    protected $casts = ['subject_id' => 'integer', 'actor_id' => 'integer', 'changes_json' => 'array'];

    public function actor(): BelongsTo { return $this->belongsTo(User::class, 'actor_id'); }
}
