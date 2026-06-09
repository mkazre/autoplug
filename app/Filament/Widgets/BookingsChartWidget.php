<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;

class BookingsChartWidget extends ChartWidget
{
    public function getHeading(): ?string
    {
        return 'Bookings (last 14 days)';
    }

    protected function getData(): array
    {
        $start = now()->subDays(13)->startOfDay();

        $grouped = Booking::where('created_at', '>=', $start)
            ->get()
            ->groupBy(fn ($b) => $b->created_at->format('Y-m-d'));

        $labels = [];
        $data = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $labels[] = $day->format('d M');
            $data[] = ($grouped[$day->format('Y-m-d')] ?? collect())->count();
        }

        return [
            'datasets' => [[
                'label' => 'Bookings',
                'data' => $data,
                'borderColor' => '#6366f1',
                'backgroundColor' => 'rgba(99,102,241,0.2)',
                'fill' => true,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
