<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Garage;
use App\Models\Payment;
use App\Models\User;
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
            Stat::make('Bookings today', Booking::whereDate('scheduled_at', today())->count()),
            Stat::make('Total bookings', Booking::count()),
            Stat::make('Users', User::count()),
            Stat::make('Revenue (paid)', 'R'.number_format((float) Payment::where('status', 'paid')->sum('amount'), 2))
                ->color('success'),
        ];
    }
}
