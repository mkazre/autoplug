<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestBookingsWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent bookings')
            ->query(Booking::query()->with(['user', 'branch.garage', 'quote'])->latest()->limit(50))
            ->columns([
                TextColumn::make('user.name')->label('Customer')->searchable(),
                TextColumn::make('branch.garage.name')->label('Garage'),
                TextColumn::make('scheduled_at')->label('Scheduled')->dateTime(),
                TextColumn::make('quote.total_price')->label('Amount')->money('ZAR'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'inprogress' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->label('Booked')->since(),
            ])
            ->paginated([5, 10, 25]);
    }
}
