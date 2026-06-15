<?php

namespace App\Filament\Resources\PlanSubscriptions\Tables;

use App\Support\PlanAudit;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PlanSubscriptionsTable
{
    private const STATUS = [
        'active' => 'Active', 'suspended' => 'Suspended', 'lapsed' => 'Lapsed',
        'cancelled' => 'Cancelled', 'completed' => 'Completed',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('user.name')->label('Holder')->searchable(),
                TextColumn::make('product.name')->label('Plan')->searchable(),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn (string $state): string => self::STATUS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'suspended', 'cancelled' => 'danger',
                        'completed' => 'gray',
                        default => 'warning',
                    }),
                TextColumn::make('start_date')->date('d M Y')->sortable(),
                TextColumn::make('end_date')->date('d M Y')->placeholder('—'),
                TextColumn::make('total_paid')->label('Paid')->formatStateUsing(fn ($s) => 'R'.number_format((float) $s, 2)),
                TextColumn::make('current_balance')->label('Balance')->formatStateUsing(fn ($s) => 'R'.number_format((float) $s, 2)),
            ])
            ->filters([
                SelectFilter::make('status')->options(self::STATUS),
            ])
            ->recordActions([
                Action::make('ledger')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Subscription ledger')
                    ->modalContent(fn ($record) => view('filament.plan-subscription', [
                        'sub' => $record->load(['user', 'product', 'installments' => fn ($q) => $q->orderBy('installment_number'), 'payments' => fn ($q) => $q->latest()]),
                    ]))
                    ->modalSubmitAction(false),
                Action::make('cancel')
                    ->icon('heroicon-o-x-circle')->color('danger')
                    ->visible(fn ($record) => in_array($record->status, ['active', 'suspended']))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'cancelled']);
                        PlanAudit::log($record, 'cancelled', [], auth()->id());
                    }),
            ]);
    }
}
