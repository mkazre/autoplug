<?php

namespace App\Support;

use App\Models\PlanAuditLog;
use Illuminate\Database\Eloquent\Model;

class PlanAudit
{
    public static function log(Model $subject, string $action, array $changes = [], ?int $actorId = null): void
    {
        PlanAuditLog::create([
            'subject_type' => $subject::class,
            'subject_id' => $subject->getKey(),
            'actor_id' => $actorId ?? auth()->id(),
            'action' => $action,
            'changes_json' => $changes ?: null,
        ]);
    }
}
