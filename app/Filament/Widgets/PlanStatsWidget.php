<?php

namespace App\Filament\Widgets;

use App\Models\GaragePayout;
use App\Models\GarageReferral;
use App\Models\PlanInstallment;
use App\Models\PlanPayment;
use App\Models\PlanRedemption;
use App\Models\PlanSubscription;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlanStatsWidget extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('Active plans', PlanSubscription::where('status', 'active')->count())
                ->description('Live cover')->descriptionIcon('heroicon-m-shield-check')->color('success'),
            Stat::make('Plan revenue', 'R'.number_format((float) PlanPayment::where('status', 'paid')->sum('amount'), 2))
                ->description('Collected')->descriptionIcon('heroicon-m-banknotes')->color('primary'),
            Stat::make('Pending claims', PlanRedemption::where('status', 'pending')->count())
                ->description('Awaiting review')->descriptionIcon('heroicon-m-clipboard-document-check')->color('warning'),
            Stat::make('Pending referrals', GarageReferral::where('reward_status', 'pending')->count())
                ->description('To approve')->descriptionIcon('heroicon-m-user-plus')->color('info'),
            Stat::make('Overdue installments', PlanInstallment::where('status', 'overdue')->count())
                ->description('Past grace')->descriptionIcon('heroicon-m-exclamation-triangle')->color('danger'),
            Stat::make('Pending payouts', 'R'.number_format((float) GaragePayout::where('status', 'pending')->sum('amount'), 2))
                ->description('Owed to garages')->descriptionIcon('heroicon-m-arrow-up-tray')->color('gray'),
        ];
    }
}
