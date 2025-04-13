<?php

namespace App\Filament\Admin\Resources\FoundationResource\Pages;

use App\Filament\Admin\Resources\FoundationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFoundation extends EditRecord
{
    protected static string $resource = FoundationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
