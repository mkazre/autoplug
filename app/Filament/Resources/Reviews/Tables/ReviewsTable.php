<?php

namespace App\Filament\Resources\Reviews\Tables;

use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('garage.name')->label('Garage')->searchable(),
                TextColumn::make('user.name')->label('Customer')->searchable(),
                TextColumn::make('rating')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn (int $state): string => str_repeat('★', $state)),
                TextColumn::make('comment')->limit(60)->placeholder('—'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('rating')->options([
                    1 => '1 ★', 2 => '2 ★', 3 => '3 ★', 4 => '4 ★', 5 => '5 ★',
                ]),
            ])
            ->recordActions([
                DeleteAction::make(),
            ]);
    }
}
