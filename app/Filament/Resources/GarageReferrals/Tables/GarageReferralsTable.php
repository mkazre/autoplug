<?php

namespace App\Filament\Resources\GarageReferrals\Tables;

use App\Support\PlanReferralService;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GarageReferralsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('garage.name')->label('Referrer')->searchable(),
                TextColumn::make('referredUser.name')->label('Customer')->searchable(),
                TextColumn::make('subscription.product.name')->label('Plan')->placeholder('—'),
                TextColumn::make('reward_amount')->label('Reward')->formatStateUsing(fn ($s) => 'R'.number_format((float) $s, 2)),
                TextColumn::make('reward_status')->badge()->color(fn (string $state): string => match ($state) {
                    'paid' => 'success', 'approved' => 'info', default => 'warning',
                }),
                TextColumn::make('created_at')->dateTime('d M Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('reward_status')->options(['pending' => 'Pending', 'approved' => 'Approved', 'paid' => 'Paid']),
            ])
            ->recordActions([
                Action::make('approve')
                    ->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn ($record) => $record->reward_status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn ($record) => PlanReferralService::approve($record, auth()->id())),
            ]);
    }
}
