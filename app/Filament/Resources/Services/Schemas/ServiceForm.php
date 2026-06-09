<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ServiceForm
{
    public const CATEGORIES = [
        'minor_service' => 'Minor Service',
        'major_service' => 'Major Service',
        'tyres' => 'Tyres',
        'brakes' => 'Brakes',
        'repair' => 'Repair',
        'other' => 'Other',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            Select::make('category')
                ->options(self::CATEGORIES)
                ->required()
                ->default('other'),
        ]);
    }
}
