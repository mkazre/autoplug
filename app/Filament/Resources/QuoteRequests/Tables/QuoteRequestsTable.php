<?php

namespace App\Filament\Resources\QuoteRequests\Tables;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuoteRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table->poll('15s')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('user.name')->label('Customer')->searchable(),
                TextColumn::make('user.email')->label('Email')->toggleable(),
                TextColumn::make('user.phone')->label('Phone')->toggleable()->placeholder('—'),
                TextColumn::make('service.name')->label('Service')->placeholder('General'),
                TextColumn::make('vehicle.make')->label('Make')->toggleable()->placeholder('—'),
                TextColumn::make('vehicle.model')->label('Model')->toggleable()->placeholder('—'),
                TextColumn::make('description')->limit(40)->toggleable()->placeholder('—'),
                ImageColumn::make('images')
                    ->label('Photos')
                    ->disk('public')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->toggleable(),
                TextColumn::make('request_garages_count')->counts('requestGarages')->label('Garages'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'warning',
                        'closed' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'open' => 'Open',
                    'closed' => 'Closed',
                    'cancelled' => 'Cancelled',
                ]),
            ]);
    }
}
