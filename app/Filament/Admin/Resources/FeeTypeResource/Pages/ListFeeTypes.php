<?php

namespace App\Filament\Admin\Resources\FeeTypeResource\Pages;

use App\Filament\Admin\Resources\FeeTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeeTypes extends ListRecords
{
    protected static string $resource = FeeTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
