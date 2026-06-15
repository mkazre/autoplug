<?php

namespace App\Filament\Resources\Plans\Pages;

use App\Filament\Resources\Plans\PlanProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPlanProducts extends ListRecords
{
    protected static string $resource = PlanProductResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
