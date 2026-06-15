<?php

namespace App\Filament\Resources\Plans;

use App\Filament\Resources\Plans\Pages\CreatePlanProduct;
use App\Filament\Resources\Plans\Pages\EditPlanProduct;
use App\Filament\Resources\Plans\Pages\ListPlanProducts;
use App\Filament\Resources\Plans\Schemas\PlanProductForm;
use App\Filament\Resources\Plans\Tables\PlanProductsTable;
use App\Models\PlanProduct;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PlanProductResource extends Resource
{
    protected static ?string $model = PlanProduct::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Plan products';

    protected static string|\UnitEnum|null $navigationGroup = 'Plans';

    public static function form(Schema $schema): Schema
    {
        return PlanProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlanProductsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlanProducts::route('/'),
            'create' => CreatePlanProduct::route('/create'),
            'edit' => EditPlanProduct::route('/{record}/edit'),
        ];
    }
}
