<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Payment;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonthlyCashFlowStats extends BaseWidget
{

    public static function canView(): bool
{
    return auth()->user()?->role === 'foundation';
}
    protected function getCards(): array
    {
        $year = now()->year;
        $foundationId = Auth::user()->foundation_id;

        $totalIncome = DB::table('payments')
            ->whereYear('payment_date', $year)
            ->where('foundation_id', $foundationId)
            ->whereNull('deleted_at')
            ->sum('paid_amount');

        $totalExpense = DB::table('expenses')
            ->whereYear('date', $year)
            ->where('foundation_id', $foundationId)
            ->whereNull('deleted_at')
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        return [
            Stat::make('Total Pemasukan', 'Rp ' . number_format($totalIncome, 0, ',', '.')),
            Stat::make('Total Pengeluaran', 'Rp ' . number_format($totalExpense, 0, ',', '.')),
            Stat::make('Saldo', 'Rp ' . number_format($balance, 0, ',', '.')),
        ];
    }

    public static function getColumnsSpan(): int|string|array
    {
        return 'full'; // Full width supaya kelihatan rapi di atas chart
    }
}
