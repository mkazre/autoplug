<?php

namespace App\Filament\Resources\PlanAuditLogs;

use App\Filament\Resources\PlanAuditLogs\Pages\ListPlanAuditLogs;
use App\Filament\Resources\PlanAuditLogs\Tables\PlanAuditLogsTable;
use App\Models\PlanAuditLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class PlanAuditLogResource extends Resource
{
    protected static ?string $model = PlanAuditLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Audit log';

    protected static string|\UnitEnum|null $navigationGroup = 'Plans';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return PlanAuditLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return ['index' => ListPlanAuditLogs::route('/')];
    }
}
