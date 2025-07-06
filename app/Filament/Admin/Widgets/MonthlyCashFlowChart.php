<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Forms;
use App\Models\Foundation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonthlyCashFlowChart extends ChartWidget
{
    protected static ?string $heading = 'Arus Kas Bulanan';

    public static function canView(): bool
{
    return auth()->user()?->role === 'foundation';
}

    public function getColumnSpan(): int|string|array
{
    return 'full';
}

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('foundation_id')
                ->label('Yayasan')
                ->options(Foundation::pluck('name', 'id'))
                ->required()
                ->default(fn() => Auth::user()->foundation_id)
                ->visible(fn() => Auth::user()->role === 'superadmin')
                ->disabled(fn() => Auth::user()->role !== 'superadmin'),
        ];
    }

    protected function getData(): array
    {
        $year = now()->year;
        $foundationId = $this->filterFormData['foundation_id'] ?? Auth::user()->foundation_id;

        $income = DB::table('payments')
            ->selectRaw('MONTH(payment_date) as month, SUM(paid_amount) as total')
            ->whereYear('payment_date', $year)
            ->where('foundation_id', $foundationId)
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('MONTH(payment_date)'))
            ->pluck('total', 'month');

        $expense = DB::table('expenses')
            ->whereYear('date', $year)
            ->where('foundation_id', $foundationId)
            ->whereNull('deleted_at')
            ->selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $incomeData = [];
        $expenseData = [];

        foreach (range(1, 12) as $month) {
            $labels[] = now()->startOfYear()->addMonths($month - 1)->format('M');
            $incomeData[] = $income[$month] ?? 0;
            $expenseData[] = $expense[$month] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan',
                    'data' => $incomeData,
                    'borderColor' => '#4ade80',
                    'backgroundColor' => '#4ade80',
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => $expenseData,
                    'borderColor' => '#f87171',
                    'backgroundColor' => '#f87171',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
