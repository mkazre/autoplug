<?php

namespace App\Filament\Resources\Garages\Tables;

use App\Models\Garage;
use App\Notifications\GarageStatusUpdated;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GaragesTable
{
    public static function configure(Table $table): Table
    {
        return $table->poll('15s')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Garage')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                IconColumn::make('verified')->boolean()->label('Verified'),
                TextColumn::make('reviewed_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Applied')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Garage $record): bool => $record->status !== 'approved')
                    ->requiresConfirmation()
                    ->modalHeading('Approve this garage?')
                    ->action(function (Garage $record): void {
                        $record->update(['status' => 'approved', 'reviewed_at' => now()]);
                        $record->user?->notify(new GarageStatusUpdated($record));
                        Notification::make()->title('Garage approved')->success()->send();
                    }),
                Action::make('requestInfo')
                    ->label('Request info')
                    ->icon('heroicon-o-question-mark-circle')
                    ->color('warning')
                    ->schema([
                        Textarea::make('admin_notes')
                            ->label('What information do you need from the applicant?')
                            ->required(),
                    ])
                    ->modalHeading('Request more information')
                    ->action(function (array $data, Garage $record): void {
                        $record->update(['admin_notes' => $data['admin_notes']]);
                        $record->user?->notify(new GarageStatusUpdated($record, 'info_requested'));
                        Notification::make()->title('Information requested')->success()->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Garage $record): bool => $record->status !== 'rejected')
                    ->schema([
                        Textarea::make('admin_notes')
                            ->label('Reason for rejection')
                            ->required(),
                    ])
                    ->modalHeading('Reject this garage?')
                    ->action(function (array $data, Garage $record): void {
                        $record->update([
                            'status' => 'rejected',
                            'admin_notes' => $data['admin_notes'],
                            'reviewed_at' => now(),
                        ]);
                        $record->user?->notify(new GarageStatusUpdated($record));
                        Notification::make()->title('Garage rejected')->danger()->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
