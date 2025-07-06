<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;

class ParentPaymentsStatus extends BaseWidget
{
    protected static ?string $heading = 'Status Pembayaran Saya';

    protected static ?int $sort = 1;

      public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    public static function canView(): bool
    {
        return Auth::user()?->role === 'parent'; 
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();

        return $table
            ->query(
                Student::query()
                    ->where('guardian_id', $user->id)
                    ->with(['studentAcademics.academicYear', 'studentAcademics.payments'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Anak'),

                Tables\Columns\TextColumn::make('studentAcademics.academicYear.name')
                    ->label('Tahun Ajaran')
                    ->formatStateUsing(fn ($record) => 
                        optional($record->studentAcademics->last()?->academicYear)->name ?? '-'
                    ),

                Tables\Columns\TextColumn::make('payments_sum')
                    ->label('Total Bayar')
                    ->formatStateUsing(fn ($record) =>
                        'Rp ' . number_format(
                            $record->studentAcademics->flatMap->payments->sum('paid_amount'),
                            0, ',', '.'
                        )
                    ),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(function ($record) {
                        $totalFee = $record->studentAcademics->flatMap->academicYear->flatMap->fees->sum('amount') ?? 0;
                        $totalPaid = $record->studentAcademics->flatMap->payments->sum('paid_amount');

                        if ($totalPaid >= $totalFee) {
                            return 'Lunas';
                        }

                        return 'Belum Lunas';
                    }),
            ]);
    }
}
