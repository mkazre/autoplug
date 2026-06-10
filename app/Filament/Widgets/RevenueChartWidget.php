<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class RevenueChartWidget extends ChartWidget
{
    public function getHeading(): ?string
    {
        return 'Revenue: paid vs pending';
    }

    protected function getData(): array
    {
        $paid = (float) Payment::where('status', 'paid')->sum('amount');
        $pending = (float) Payment::where('status', 'pending')->sum('amount');
        $failed = (float) Payment::where('status', 'failed')->sum('amount');

        return [
            'datasets' => [[
                'data' => [$paid, $pending, $failed],
                'backgroundColor' => ['#16a34a', '#f59e0b', '#dc2626'],
            ]],
            'labels' => ['Paid', 'Pending', 'Failed'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
