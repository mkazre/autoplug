<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone')->placeholder('—'),
                TextColumn::make('role')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'admin' => 'danger',
                        'garage_owner' => 'warning',
                        'car_owner' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('fleet.name')->label('Fleet')->placeholder('—')->toggleable(),
                TextColumn::make('created_at')->label('Registered')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('role')->options([
                    'admin' => 'Admin',
                    'car_owner' => 'Car owner',
                    'garage_owner' => 'Garage owner',
                ]),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
