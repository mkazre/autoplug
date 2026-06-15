<?php

namespace App\Filament\Resources\PlanRedemptions;

use App\Filament\Resources\PlanRedemptions\Pages\ListPlanRedemptions;
use App\Filament\Resources\PlanRedemptions\Tables\PlanRedemptionsTable;
use App\Models\PlanRedemption;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class PlanRedemptionResource extends Resource
{
    protected static ?string $model = PlanRedemption::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Redemptions';

    protected static string|\UnitEnum|null $navigationGroup = 'Plans';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return PlanRedemptionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlanRedemptions::route('/'),
        ];
    }
}
