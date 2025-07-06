<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Foundation;

class ActiveFoundationStats extends BaseWidget
{
    protected static ?string $componentName = 'active-foundation-stats'; // [TAMBAHKAN INI]
    
    protected ?string $heading = 'Statistik Yayasan Aktif';

    public static function canView(): bool
    {
        return auth()->user()?->role === 'superadmin';
    }

    public function getColumnSpan(): int|string|array 
    {
        return 'full';
    }

    protected function getCards(): array
    {
        return [
            Card::make('Yayasan Aktif', Foundation::count()),
        ];
    }
}