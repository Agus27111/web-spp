<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\AccountWidget as BaseAccountWidget;

class CustomAccountWidget extends BaseAccountWidget
{
    public function getColumnSpan(): int|string|array 
    {
        return 'full';
    }
}