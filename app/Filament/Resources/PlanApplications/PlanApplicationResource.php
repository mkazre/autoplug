<?php

namespace App\Filament\Resources\PlanApplications;

use App\Filament\Resources\PlanApplications\Pages\ListPlanApplications;
use App\Filament\Resources\PlanApplications\Tables\PlanApplicationsTable;
use App\Models\PlanApplication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class PlanApplicationResource extends Resource
{
    protected static ?string $model = PlanApplication::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $navigationLabel = 'Applications';

    protected static string|\UnitEnum|null $navigationGroup = 'Plans';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return PlanApplicationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlanApplications::route('/'),
        ];
    }
}
