<?php

namespace App\Filament\Widgets;

use App\Models\QuoteRequest;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestQuoteRequestsWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent quote requests')
            ->query(QuoteRequest::query()->with(['user', 'service'])->withCount('requestGarages')->latest()->limit(50))
            ->columns([
                TextColumn::make('user.name')->label('Customer')->searchable(),
                TextColumn::make('user.phone')->label('Phone')->placeholder('—'),
                TextColumn::make('service.name')->label('Service')->placeholder('General'),
                TextColumn::make('request_garages_count')->label('Garages'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'warning',
                        'closed' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->since(),
            ])
            ->paginated([5, 10, 25]);
    }
}
