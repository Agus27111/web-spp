<?php

namespace App\Filament\Admin\Resources\IncomeResource\Pages;

use App\Filament\Admin\Resources\IncomeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIncome extends EditRecord
{
    protected static string $resource = IncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
