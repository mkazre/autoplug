<?php

namespace App\Filament\Resources\Bidders\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BiddersTable
{
    private const STATUS_LABELS = [
        'pending' => 'Awaiting quote',
        'quoted' => 'Quoted',
        'accepted' => 'Won',
        'declined' => 'Lost',
        'expired' => 'Expired',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('branch.garage.name')
                    ->label('Garage')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('branch.name')
                    ->label('Branch')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('quoteRequest.user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('quoteRequest.service.name')
                    ->label('Service')
                    ->placeholder('General')
                    ->toggleable(),
                TextColumn::make('quote.total_price')
                    ->label('Quote')
                    ->formatStateUsing(fn ($state) => $state !== null ? 'R'.number_format((float) $state, 2) : '—')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Outcome')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::STATUS_LABELS[$state] ?? ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'accepted' => 'success',
                        'declined' => 'danger',
                        'quoted' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(self::STATUS_LABELS),
            ]);
    }
}
