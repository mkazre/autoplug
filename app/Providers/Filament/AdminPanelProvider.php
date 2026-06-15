<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\BidderStatsWidget;
use App\Filament\Widgets\BookingsChartWidget;
use App\Filament\Widgets\GarageStatsWidget;
use App\Filament\Widgets\PlanStatsWidget;
use App\Filament\Widgets\LatestBookingsWidget;
use App\Filament\Widgets\LatestQuoteRequestsWidget;
use App\Filament\Widgets\QuotesChartWidget;
use App\Filament\Widgets\RevenueChartWidget;
use App\Support\Settings;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        try {
            $brand = Settings::get('brand_name', 'Autoplug');
            $logo = Settings::get('logo');
            $favicon = Settings::get('favicon');
            $primary = Settings::get('primary_color');
            $logoHeight = Settings::int('logo_height', 40);
        } catch (\Throwable $e) {
            $brand = 'Autoplug';
            $logo = $favicon = $primary = null;
            $logoHeight = 40;
        }

        $css = '<style>'
            .'.fi-wi-stats-overview-stat{border-radius:1rem;border:1px solid rgba(0,0,0,.04);}'
            .'.fi-wi-stats-overview-stat:nth-child(8n+1){background:linear-gradient(135deg,#ede9fe,#f5f3ff);}'
            .'.fi-wi-stats-overview-stat:nth-child(8n+2){background:linear-gradient(135deg,#ffedd5,#fff7ed);}'
            .'.fi-wi-stats-overview-stat:nth-child(8n+3){background:linear-gradient(135deg,#dcfce7,#ecfdf5);}'
            .'.fi-wi-stats-overview-stat:nth-child(8n+4){background:linear-gradient(135deg,#dbeafe,#eff6ff);}'
            .'.fi-wi-stats-overview-stat:nth-child(8n+5){background:linear-gradient(135deg,#fae8ff,#fdf4ff);}'
            .'.fi-wi-stats-overview-stat:nth-child(8n+6){background:linear-gradient(135deg,#e2e8f0,#f8fafc);}'
            .'.fi-wi-stats-overview-stat:nth-child(8n+7){background:linear-gradient(135deg,#e0e7ff,#eef2ff);}'
            .'.fi-wi-stats-overview-stat:nth-child(8n){background:linear-gradient(135deg,#fef9c3,#fefce8);}'
            .'</style>';

        $chime = '<audio id="ap-chime" src="/sounds/notify.wav" preload="auto"></audio>'
            .'<script>(function(){var last=null;'
            .'function poll(){fetch("/platform-activity",{headers:{"Accept":"application/json"}}).then(function(r){return r.json();}).then(function(d){if(last!==null&&d.count>last){var a=document.getElementById("ap-chime");if(a){try{a.currentTime=0;a.play();}catch(e){}}}last=d.count;}).catch(function(){});}'
            .'function prime(){var a=document.getElementById("ap-chime");if(a){a.muted=true;a.play().then(function(){a.pause();a.currentTime=0;a.muted=false;}).catch(function(){a.muted=false;});}document.removeEventListener("pointerdown",prime);document.removeEventListener("keydown",prime);}'
            .'document.addEventListener("pointerdown",prime);document.addEventListener("keydown",prime);'
            .'poll();setInterval(poll,15000);})();</script>';

        $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->databaseNotifications()
            ->databaseNotificationsPolling('15s')
            ->sidebarWidth('15rem')
            ->maxContentWidth('full')
            ->brandName($brand)
            ->brandLogoHeight($logoHeight.'px')
            ->colors([
                'primary' => $primary ? Color::hex($primary) : Color::Violet,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                GarageStatsWidget::class,
                BidderStatsWidget::class,
                PlanStatsWidget::class,
                BookingsChartWidget::class,
                QuotesChartWidget::class,
                RevenueChartWidget::class,
                LatestBookingsWidget::class,
                LatestQuoteRequestsWidget::class,
            ])
            ->navigationItems([
                NavigationItem::make('Settings')
                    ->url(fn (): string => url('/platform-settings'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->sort(99),
            ])
            ->renderHook('panels::head.end', fn (): string => $css)
            ->renderHook('panels::body.end', fn (): string => $chime)
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);

        if ($logo) {
            $panel->brandLogo(asset('storage/'.$logo));
        }
        if ($favicon) {
            $panel->favicon(asset('storage/'.$favicon));
        }

        return $panel;
    }
}
