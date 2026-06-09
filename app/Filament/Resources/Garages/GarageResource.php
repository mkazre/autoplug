<?php

namespace App\Filament\Resources\Garages;

use App\Filament\Resources\Garages\Pages\CreateGarage;
use App\Filament\Resources\Garages\Pages\EditGarage;
use App\Filament\Resources\Garages\Pages\ListGarages;
use App\Filament\Resources\Garages\Schemas\GarageForm;
use App\Filament\Resources\Garages\Tables\GaragesTable;
use App\Models\Garage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GarageResource extends Resource
{
    protected static ?string $model = Garage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Garages';

    public static function form(Schema $schema): Schema
    {
        return GarageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GaragesTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('status', 'pending')->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGarages::route('/'),
            'create' => CreateGarage::route('/create'),
            'edit' => EditGarage::route('/{record}/edit'),
        ];
    }
}
