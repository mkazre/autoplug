<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Resources\Garages\GarageResource;
use App\Filament\Resources\Payments\PaymentResource;
use App\Filament\Resources\QuoteRequests\QuoteRequestResource;
use App\Filament\Resources\Quotes\QuoteResource;
use App\Filament\Resources\Reviews\ReviewResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Booking;
use App\Models\Garage;
use App\Models\Payment;
use App\Models\Quote;
use App\Models\QuoteRequest;
use App\Models\Review;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GarageStatsWidget extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('Total sales (paid)', 'R'.number_format((float) Payment::where('status', 'paid')->sum('amount'), 2))
                ->description('Revenue received')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart($this->dailyPaid())
                ->url(PaymentResource::getUrl('index')),
            Stat::make('Pending revenue', 'R'.number_format((float) Payment::where('status', 'pending')->sum('amount'), 2))
                ->description('Awaiting payment')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->url(PaymentResource::getUrl('index')),
            Stat::make('Total bookings', Booking::count())
                ->description(Booking::whereDate('scheduled_at', today())->count().' today')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary')
                ->chart($this->daily(Booking::class))
                ->url(BookingResource::getUrl('index')),
            Stat::make('Quote requests', QuoteRequest::count())
                ->description('From customers')
                ->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->color('info')
                ->chart($this->daily(QuoteRequest::class))
                ->url(QuoteRequestResource::getUrl('index')),
            Stat::make('Quotes sent', Quote::count())
                ->description('By garages')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->chart($this->daily(Quote::class))
                ->url(QuoteResource::getUrl('index')),
            Stat::make('Garages', Garage::count())
                ->description(Garage::where('status', 'pending')->count().' pending')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('gray')
                ->url(GarageResource::getUrl('index')),
            Stat::make('Users', User::count())
                ->description('Registered')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->chart($this->daily(User::class))
                ->url(UserResource::getUrl('index')),
            Stat::make('Reviews', Review::count())
                ->description(number_format((float) Review::avg('rating'), 1).' avg rating')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning')
                ->url(ReviewResource::getUrl('index')),
        ];
    }

    private function daily(string $model): array
    {
        $start = now()->subDays(6)->startOfDay();
        $rows = $model::where('created_at', '>=', $start)->get()->groupBy(fn ($r) => $r->created_at->format('Y-m-d'));
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $data[] = ($rows[now()->subDays($i)->format('Y-m-d')] ?? collect())->count();
        }

        return $data;
    }

    private function dailyPaid(): array
    {
        $start = now()->subDays(6)->startOfDay();
        $rows = Payment::where('status', 'paid')->where('created_at', '>=', $start)->get()->groupBy(fn ($r) => $r->created_at->format('Y-m-d'));
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $data[] = (float) ($rows[now()->subDays($i)->format('Y-m-d')] ?? collect())->sum('amount');
        }

        return $data;
    }
}
