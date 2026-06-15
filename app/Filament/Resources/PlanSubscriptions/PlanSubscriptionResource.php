<?php

namespace App\Filament\Resources\PlanSubscriptions;

use App\Filament\Resources\PlanSubscriptions\Pages\ListPlanSubscriptions;
use App\Filament\Resources\PlanSubscriptions\Tables\PlanSubscriptionsTable;
use App\Models\PlanSubscription;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class PlanSubscriptionResource extends Resource
{
    protected static ?string $model = PlanSubscription::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Subscriptions';

    protected static string|\UnitEnum|null $navigationGroup = 'Plans';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return PlanSubscriptionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlanSubscriptions::route('/'),
        ];
    }
}
