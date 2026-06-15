<?php

namespace App\Filament\Resources\PlanAuditLogs\Pages;

use App\Filament\Resources\PlanAuditLogs\PlanAuditLogResource;
use Filament\Resources\Pages\ListRecords;

class ListPlanAuditLogs extends ListRecords
{
    protected static string $resource = PlanAuditLogResource::class;
}
