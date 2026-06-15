<?php

namespace App\Filament\Resources\GaragePayouts\Tables;

use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GaragePayoutsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('garage.name')->label('Garage')->searchable(),
                TextColumn::make('type')->badge()->formatStateUsing(fn ($s) => ucfirst(str_replace('_', ' ', (string) $s))),
                TextColumn::make('amount')->formatStateUsing(fn ($s) => 'R'.number_format((float) $s, 2))->sortable(),
                TextColumn::make('status')->badge()->color(fn (string $state): string => $state === 'paid' ? 'success' : 'warning'),
                TextColumn::make('cycle')->placeholder('—'),
                TextColumn::make('paid_at')->dateTime('d M Y')->placeholder('—'),
                TextColumn::make('created_at')->dateTime('d M Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')->options(['redemption' => 'Redemption', 'discount_subsidy' => 'Discount subsidy', 'referral' => 'Referral']),
                SelectFilter::make('status')->options(['pending' => 'Pending', 'paid' => 'Paid']),
            ])
            ->recordActions([
                Action::make('markPaid')
                    ->icon('heroicon-o-check')->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'paid', 'paid_at' => now(), 'cycle' => $record->cycle ?? 'manual-'.now()->format('Y-m-d')])),
            ]);
    }
}
