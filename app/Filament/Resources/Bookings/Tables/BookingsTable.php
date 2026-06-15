<?php

namespace App\Filament\Resources\Bookings\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table->poll('15s')
            ->defaultSort('scheduled_at', 'desc')
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('user.name')->label('Customer')->searchable(),
                TextColumn::make('branch.garage.name')->label('Garage')->searchable(),
                TextColumn::make('branch.name')->label('Branch')->toggleable(),
                TextColumn::make('quote.quoteRequestGarage.quoteRequest.service.name')->label('Service')->placeholder('General'),
                TextColumn::make('scheduled_at')->label('Scheduled')->dateTime()->sortable(),
                TextColumn::make('quote.total_price')->label('Amount')->money('ZAR'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'inprogress' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('payment.status')
                    ->label('Payment')
                    ->badge()
                    ->placeholder('—')
                    ->color(fn (?string $state): string => match ($state) {
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->label('Booked')->dateTime()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'inprogress' => 'In progress',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ]),
            ]);
    }
}
