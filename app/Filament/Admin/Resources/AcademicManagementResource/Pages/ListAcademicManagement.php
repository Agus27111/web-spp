<?php

namespace App\Filament\Admin\Resources\AcademicManagementResource\Pages;

use App\Filament\Admin\Resources\AcademicManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcademicManagement extends ListRecords
{
    protected static string $resource = AcademicManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
