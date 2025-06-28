<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Incoming;
use App\Models\Outgoing;
use App\Models\Submittel;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static string $color = 'info';
    protected function getStats(): array
    {
        return [
            Stat::make('Categories', Category::count())
            ->description('Total Categories'),
            Stat::make('Incmomings', Incoming::count())
            ->description('Total incomig files'),
            Stat::make('Outgoings', Outgoing::count())
            ->description('Total outgoing files'),
            Stat::make('Submittles', Submittel::count())
            ->description('Total submittles'),
        ];
    }
}
