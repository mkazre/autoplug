<?php

namespace App\Filament\Resources\Plans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PlanProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('type')->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'maintenance' ? 'Maintenance' : 'Service')
                    ->color(fn (string $state): string => $state === 'maintenance' ? 'warning' : 'info'),
                TextColumn::make('base_price')->label('Base price')
                    ->formatStateUsing(fn ($state) => 'R'.number_format((float) $state, 2))->sortable(),
                TextColumn::make('max_km')->label('Max km')->numeric()->placeholder('—'),
                TextColumn::make('max_age_years')->label('Max age')
                    ->formatStateUsing(fn ($state) => $state ? $state.' yrs' : '—'),
                TextColumn::make('pricing_tiers_count')->counts('pricingTiers')->label('Tiers'),
                IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->filters([
                SelectFilter::make('type')->options(['service' => 'Service', 'maintenance' => 'Maintenance']),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
