<?php

namespace App\Filament\Widgets;

use App\Models\Quote;
use Filament\Widgets\ChartWidget;

class QuotesChartWidget extends ChartWidget
{
    public function getHeading(): ?string
    {
        return 'Quotes (last 14 days)';
    }

    protected function getData(): array
    {
        $start = now()->subDays(13)->startOfDay();

        $grouped = Quote::where('created_at', '>=', $start)
            ->get()
            ->groupBy(fn ($q) => $q->created_at->format('Y-m-d'));

        $labels = [];
        $data = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $labels[] = $day->format('d M');
            $data[] = ($grouped[$day->format('Y-m-d')] ?? collect())->count();
        }

        return [
            'datasets' => [[
                'label' => 'Quotes',
                'data' => $data,
                'backgroundColor' => '#f59e0b',
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
