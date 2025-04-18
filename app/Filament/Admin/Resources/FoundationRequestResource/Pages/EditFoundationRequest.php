<?php

namespace App\Filament\Admin\Resources\FoundationRequestResource\Pages;

use App\Filament\Admin\Resources\FoundationRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFoundationRequest extends EditRecord
{
    protected static string $resource = FoundationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
