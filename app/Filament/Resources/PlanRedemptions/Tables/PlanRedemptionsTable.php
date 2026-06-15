<?php

namespace App\Filament\Resources\PlanRedemptions\Tables;

use App\Support\PlanRedemptionService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PlanRedemptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('branch.garage.name')->label('Garage')->searchable(),
                TextColumn::make('subscription.user.name')->label('Customer')->searchable(),
                TextColumn::make('redemption_type')->label('Type')->badge()
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', (string) $state))),
                TextColumn::make('amount_claimed')->label('Claimed')->formatStateUsing(fn ($s) => 'R'.number_format((float) $s, 2)),
                TextColumn::make('amount_approved')->label('Approved')->formatStateUsing(fn ($s) => $s !== null ? 'R'.number_format((float) $s, 2) : '—'),
                TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'approved' => 'success', 'rejected' => 'danger', default => 'warning',
                }),
                TextColumn::make('created_at')->dateTime('d M Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']),
            ])
            ->recordActions([
                Action::make('details')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Claim details')
                    ->modalContent(fn ($record) => view('filament.plan-redemption', [
                        'r' => $record->load('subscription.user', 'subscription.product', 'branch.garage'),
                    ]))
                    ->modalSubmitAction(false),
                Action::make('approve')
                    ->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->fillForm(fn ($record) => ['amount_approved' => $record->amount_claimed])
                    ->schema([TextInput::make('amount_approved')->numeric()->prefix('R')->required()])
                    ->action(fn (array $data, $record) => PlanRedemptionService::approve($record, (float) $data['amount_approved'], auth()->id())),
                Action::make('reject')
                    ->icon('heroicon-o-x-circle')->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn ($record) => PlanRedemptionService::reject($record, auth()->id())),
            ]);
    }
}
