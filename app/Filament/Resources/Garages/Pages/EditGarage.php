<?php

namespace App\Filament\Resources\Garages\Pages;

use App\Filament\Resources\Garages\GarageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGarage extends EditRecord
{
    protected static string $resource = GarageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
