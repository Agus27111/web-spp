<?php

namespace App\Filament\Admin\Resources\AcademicManagementResource\Pages;

use App\Filament\Admin\Resources\AcademicManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcademicManagement extends EditRecord
{
    protected static string $resource = AcademicManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
