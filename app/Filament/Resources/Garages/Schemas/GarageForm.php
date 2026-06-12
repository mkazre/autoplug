<?php

namespace App\Filament\Resources\Garages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GarageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Owner')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->label('Garage name')
                    ->required(),
                TextInput::make('logo')
                    ->default(null),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->default('pending'),
                Toggle::make('verified')
                    ->label('Verified garage')
                    ->helperText('Shows a green "Verified" badge on the public site.'),
                Textarea::make('admin_notes')
                    ->label('Admin notes')
                    ->default(null)
                    ->columnSpanFull(),
                DateTimePicker::make('reviewed_at'),
            ]);
    }
}
