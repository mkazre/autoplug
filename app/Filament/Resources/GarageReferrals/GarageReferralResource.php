<?php

namespace App\Filament\Resources\GarageReferrals;

use App\Filament\Resources\GarageReferrals\Pages\ListGarageReferrals;
use App\Filament\Resources\GarageReferrals\Tables\GarageReferralsTable;
use App\Models\GarageReferral;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class GarageReferralResource extends Resource
{
    protected static ?string $model = GarageReferral::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationLabel = 'Referrals';

    protected static string|\UnitEnum|null $navigationGroup = 'Plans';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return GarageReferralsTable::configure($table);
    }

    public static function getPages(): array
    {
        return ['index' => ListGarageReferrals::route('/')];
    }
}
