<?php

namespace App\Filament\Resources\PlanPayments\Tables;

use App\Support\PlanPaymentService;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PlanPaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('subscription.user.name')->label('Customer')->searchable(),
                TextColumn::make('amount')->formatStateUsing(fn ($s) => 'R'.number_format((float) $s, 2))->sortable(),
                TextColumn::make('method')->badge()->formatStateUsing(fn ($s) => strtoupper((string) $s)),
                TextColumn::make('gateway')->badge(),
                TextColumn::make('txn_reference')->label('Txn ref')->placeholder('—')->searchable(),
                TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'paid' => 'success', 'failed' => 'danger', default => 'warning',
                }),
                TextColumn::make('paid_on')->date('d M Y')->placeholder('—'),
                TextColumn::make('created_at')->dateTime('d M Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed']),
                SelectFilter::make('gateway')->options(['payfast' => 'Card (PayFast)', 'manual' => 'Manual (EFT/Deposit)']),
            ])
            ->recordActions([
                Action::make('details')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Payment details')
                    ->modalContent(fn ($record) => view('filament.plan-payment', [
                        'p' => $record->load('subscription.user', 'installment'),
                    ]))
                    ->modalSubmitAction(false),
                Action::make('verify')
                    ->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn ($record) => $record->gateway === 'manual' && $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn ($record) => PlanPaymentService::verify($record, auth()->id())),
                Action::make('reject')
                    ->icon('heroicon-o-x-circle')->color('danger')
                    ->visible(fn ($record) => $record->gateway === 'manual' && $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn ($record) => PlanPaymentService::reject($record, auth()->id())),
            ]);
    }
}
