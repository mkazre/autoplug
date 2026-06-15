<?php

namespace App\Filament\Resources\PlanApplications\Tables;

use App\Support\PlanLifecycle;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PlanApplicationsTable
{
    private const STATUS = [
        'draft' => 'Draft', 'submitted' => 'Submitted', 'under_review' => 'Under review',
        'approved' => 'Approved', 'rejected' => 'Rejected', 'active' => 'Active',
        'expired' => 'Expired', 'cancelled' => 'Cancelled',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('user.name')->label('Customer')->searchable(),
                TextColumn::make('product.name')->label('Plan')->searchable(),
                TextColumn::make('tier.vehicle_category')->label('Tier')->placeholder('—'),
                TextColumn::make('payment_method')->label('Pay')->badge(),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn (string $state): string => self::STATUS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'approved', 'active' => 'success',
                        'rejected', 'cancelled' => 'danger',
                        'under_review' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('submitted_at')->dateTime('d M Y H:i')->sortable()->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('status')->options(self::STATUS),
            ])
            ->recordActions([
                Action::make('details')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Application details')
                    ->modalContent(fn ($record) => view('filament.plan-application', [
                        'app' => $record->load('user', 'vehicle', 'product', 'tier', 'documents', 'signature'),
                    ]))
                    ->modalSubmitAction(false),
                Action::make('approve')
                    ->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn ($record) => in_array($record->status, ['submitted', 'under_review']))
                    ->requiresConfirmation()
                    ->action(fn ($record) => PlanLifecycle::approve($record, auth()->id())),
                Action::make('requestInfo')
                    ->label('Request info')->icon('heroicon-o-information-circle')->color('warning')
                    ->visible(fn ($record) => in_array($record->status, ['submitted', 'under_review']))
                    ->schema([Textarea::make('note')->label('What is needed?')->required()])
                    ->action(fn (array $data, $record) => PlanLifecycle::requestInfo($record, $data['note'], auth()->id())),
                Action::make('reject')
                    ->icon('heroicon-o-x-circle')->color('danger')
                    ->visible(fn ($record) => in_array($record->status, ['submitted', 'under_review']))
                    ->schema([Textarea::make('reason')->label('Reason')->required()])
                    ->action(fn (array $data, $record) => PlanLifecycle::reject($record, $data['reason'], auth()->id())),
            ]);
    }
}
