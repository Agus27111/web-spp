<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Unit;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\AcademicYear;

class StatsOverview extends BaseWidget
{
      public static function canView(): bool
{
    $user = auth()->user();
    return $user && in_array($user->role, ['foundation', 'operator']);
}

    public function getColumnSpan(): int|string|array
{
    return 'full'; // Method harus non-static
}

     protected function getCards(): array
    {
        return [
            Card::make('Unit', Unit::count()),
            Card::make('Kelas', Classroom::count()),
            Card::make('Siswa', Student::count()),
            Card::make('Tahun Ajaran Aktif', AcademicYear::where('is_active', true)->first()?->name ?? '-'),
        ];
    }
}
