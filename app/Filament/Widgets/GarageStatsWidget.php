<?php

namespace App\Filament\Widgets;

use App\Models\Garage;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GarageStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total garages', Garage::count()),
            Stat::make('Pending applications', Garage::where('status', 'pending')->count())
                ->color('warning'),
            Stat::make('Approved garages', Garage::where('status', 'approved')->count())
                ->color('success'),
            Stat::make('Revenue (paid)', 'R'.number_format((float) Payment::where('status', 'paid')->sum('amount'), 2))
                ->color('success'),
        ];
    }
}
