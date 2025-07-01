<?php

namespace App\Filament\Admin\Resources\PaymentResource\Pages;

use App\Filament\Admin\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('print')
                ->label('Cetak Struk')
                ->url(fn () => $this->getResource()::getUrl('print', ['record' => $this->record]))
                ->openUrlInNewTab()
                ->icon('heroicon-o-printer'),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Pembayaran berhasil diupdate';
    }
}
