<?php

namespace App\Filament\Resources\GaragePayouts;

use App\Filament\Resources\GaragePayouts\Pages\ListGaragePayouts;
use App\Filament\Resources\GaragePayouts\Tables\GaragePayoutsTable;
use App\Models\GaragePayout;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class GaragePayoutResource extends Resource
{
    protected static ?string $model = GaragePayout::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static ?string $navigationLabel = 'Payouts';

    protected static string|\UnitEnum|null $navigationGroup = 'Plans';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return GaragePayoutsTable::configure($table);
    }

    public static function getPages(): array
    {
        return ['index' => ListGaragePayouts::route('/')];
    }
}
