<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Forms;
use App\Models\Foundation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SppPercentageChart extends ChartWidget
{
    protected static ?string $heading = 'Persentase Pembayaran SPP';

    protected static ?string $maxHeight = '400px';

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['foundation', 'operator']);
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    protected function getFormSchema(): array
{
    return [
        Forms\Components\Card::make()
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Select::make('foundation_id')
                            ->label('Yayasan')
                            ->options(Foundation::pluck('name', 'id'))
                            ->default(fn() => Auth::user()->foundation_id)
                            ->visible(fn() => Auth::user()->role === 'superadmin')
                            ->disabled(fn() => Auth::user()->role !== 'superadmin'),
                        
                        Forms\Components\Select::make('month')
                            ->label('Bulan')
                            ->options(
                                collect(range(1, 12))->mapWithKeys(fn($m) => [
                                    $m => now()->startOfYear()->addMonths($m - 1)->translatedFormat('F')
                                ])
                            )
                            ->default(now()->month)
                            ->reactive()
                            ->required(),
                    ])
            ])
    ];
}

    protected function getData(): array
    {
        // Gunakan $this->filterData bukan $this->filterFormData
        $month = $this->filterData['month'] ?? now()->month;
        $foundationId = $this->filterData['foundation_id'] ?? Auth::user()->foundation_id;
        $year = now()->year;

        $studentsPaid = DB::table('payments')
            ->join('student_academic_years', 'payments.student_academic_year_id', '=', 'student_academic_years.id')
            ->whereMonth('payments.payment_date', $month)
            ->whereYear('payments.payment_date', $year)
            ->where('payments.foundation_id', $foundationId)
            ->whereNull('payments.deleted_at')
            ->distinct('student_academic_years.student_id')
            ->count('student_academic_years.student_id');

        $totalStudents = DB::table('student_academic_years')
            ->where('foundation_id', $foundationId)
            ->distinct('student_id')
            ->count('student_id');

        $unpaid = max($totalStudents - $studentsPaid, 0);

        return [
            'datasets' => [
                [
                    'data' => [$studentsPaid, $unpaid],
                    'backgroundColor' => ['#4ade80', '#f87171'],
                ],
            ],
            'labels' => ['Sudah Bayar', 'Belum Bayar'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    public function getDescription(): ?string
    {
        $monthNumber = $this->filterData['month'] ?? now()->month;
        $monthName = now()->startOfYear()->addMonths($monthNumber - 1)->translatedFormat('F');

        return 'Persentase pembayaran SPP untuk bulan ' . $monthName;
    }
}