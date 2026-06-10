<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('email')->email()->required()->maxLength(255),
            TextInput::make('phone')->maxLength(30),
            Select::make('role')
                ->options([
                    'admin' => 'Admin',
                    'car_owner' => 'Car owner',
                    'garage_owner' => 'Garage owner',
                ])
                ->required(),
        ]);
    }
}
