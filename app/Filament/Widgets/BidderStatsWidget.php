<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Bidders\BidderResource;
use App\Models\QuoteRequestGarage;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BidderStatsWidget extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $won = QuoteRequestGarage::where('status', 'accepted')->count();
        $lost = QuoteRequestGarage::where('status', 'declined')->count();
        $pending = QuoteRequestGarage::where('status', 'quoted')->count();
        $expired = QuoteRequestGarage::where('status', 'expired')->count();
        $decided = $won + $lost;
        $winRate = $decided > 0 ? round($won / $decided * 100) : 0;

        $url = BidderResource::getUrl('index');

        return [
            Stat::make('Bids won', $won)
                ->description('Quotes accepted by customers')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success')
                ->url($url),
            Stat::make('Bids lost', $lost)
                ->description('Customer chose another garage')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->url($url),
            Stat::make('Win rate', $winRate.'%')
                ->description($decided.' decided bid(s)')
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('primary'),
            Stat::make('Awaiting decision', $pending)
                ->description('Quoted, not yet accepted')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->url($url),
            Stat::make('Expired (no response)', $expired)
                ->description('Window closed before quoting')
                ->descriptionIcon('heroicon-m-no-symbol')
                ->color('gray')
                ->url($url),
        ];
    }
}
