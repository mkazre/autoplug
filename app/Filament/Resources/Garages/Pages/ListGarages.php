<?php

namespace App\Filament\Resources\Garages\Pages;

use App\Filament\Resources\Garages\GarageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGarages extends ListRecords
{
    protected static string $resource = GarageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
