<?php

namespace App\Filament\Widgets;

use App\Models\Incoming;
use Filament\Widgets\ChartWidget;

class IncomingChart extends ChartWidget
{
    protected static ?string $heading = 'Incomings';
    protected static ?int $sort = 3;
    protected static string $color = 'success';

    public function getData(): array
    {
        $monthlyCounts = Incoming::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');

        // Fill all months (even if no data)
        $data = [];
        foreach (range(1, 12) as $month) {
            $data[] = $monthlyCounts->get($month, 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Incomings created',
                    'data' => $data,
                ],
            ],
            'labels' => [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec'
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
