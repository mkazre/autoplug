<?php

namespace App\Filament\Resources\PlanPayments;

use App\Filament\Resources\PlanPayments\Pages\ListPlanPayments;
use App\Filament\Resources\PlanPayments\Tables\PlanPaymentsTable;
use App\Models\PlanPayment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class PlanPaymentResource extends Resource
{
    protected static ?string $model = PlanPayment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Payments';

    protected static string|\UnitEnum|null $navigationGroup = 'Plans';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return PlanPaymentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlanPayments::route('/'),
        ];
    }
}
