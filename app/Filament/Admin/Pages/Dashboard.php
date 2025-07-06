<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Admin\Widgets\ActiveFoundationStats;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
             \App\Filament\Admin\Widgets\CustomAccountWidget::class,
            ActiveFoundationStats::class,
            \App\Filament\Admin\Widgets\StatsOverview::class,
            \App\Filament\Admin\Widgets\MonthlyCashFlowStats::class, 
            \App\Filament\Admin\Widgets\MonthlyCashFlowChart::class, 
            \App\Filament\Admin\Widgets\SppPercentageChart::class,
            \App\Filament\Admin\Widgets\RecentPayments::class,
             \App\Filament\Admin\Widgets\ParentPaymentsStatus::class,
        ];
    }

    public function getColumns(): int|array
    {
       return [
            'sm' => 1,
            'md' => 4,
            'lg' => 4,
            'xl' => 6,
        ];
    }

    public function getTitle(): string
    {
        return __('Dashboard Web SPP');
    }
}
