<?php

namespace App\Filament\Resources\Bidders;

use App\Filament\Resources\Bidders\Pages\ListBidders;
use App\Filament\Resources\Bidders\Tables\BiddersTable;
use App\Models\QuoteRequestGarage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class BidderResource extends Resource
{
    protected static ?string $model = QuoteRequestGarage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Bid performance';

    protected static ?string $modelLabel = 'bid';

    protected static ?string $pluralModelLabel = 'bids';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return BiddersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBidders::route('/'),
        ];
    }
}
