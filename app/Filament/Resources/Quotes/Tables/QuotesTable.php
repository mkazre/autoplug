<?php

namespace App\Filament\Resources\Quotes\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('quoteRequestGarage.branch.garage.name')->label('Garage')->searchable(),
                TextColumn::make('quoteRequestGarage.quoteRequest.user.name')->label('Customer')->searchable(),
                TextColumn::make('total_price')->label('Total')->money('ZAR')->sortable(),
                TextColumn::make('valid_until')->date()->placeholder('—')->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'accepted' => 'success',
                        'rejected' => 'gray',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'accepted' => 'Accepted',
                    'rejected' => 'Rejected',
                ]),
            ]);
    }
}
