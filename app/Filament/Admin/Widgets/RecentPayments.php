<?php

namespace App\Filament\Admin\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Payment;

class RecentPayments extends BaseWidget
{
    protected static ?string $heading = 'Pembayaran Terbaru';

    public static function canView(): bool
{
    return in_array(auth()->user()?->role, ['foundation', 'operator']);
}


    public function getColumnSpan(): int|string|array
{
    return 'full';
}

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::with(['studentAcademicYear.student'])
                    ->latest('payment_date')
                    ->take(10)
            )
            ->columns([
                 Tables\Columns\TextColumn::make('studentAcademicYear.student.name')
                    ->label('Siswa'),
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Tanggal')
                    ->date(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Jumlah')
                    ->money('IDR', true),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode'),
            ]);
    }
}
