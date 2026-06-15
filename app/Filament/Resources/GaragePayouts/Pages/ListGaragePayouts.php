<?php

namespace App\Filament\Resources\GaragePayouts\Pages;

use App\Filament\Resources\GaragePayouts\GaragePayoutResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListGaragePayouts extends ListRecords
{
    protected static string $resource = GaragePayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('runPayouts')
                ->label('Run payout cycle now')
                ->icon('heroicon-o-banknotes')
                ->requiresConfirmation()
                ->action(function () {
                    Artisan::call('app:plan-payouts', ['--force' => true]);
                    Notification::make()->title('Payout cycle processed')->success()->send();
                }),
        ];
    }
}
