<?php

namespace App\Filament\Admin\Resources\PaymentResource\Pages;

use App\Filament\Admin\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

       protected function mutateFormDataBeforeCreate(array $data): array
    {
        return PaymentResource::beforeCreate($data);
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {

         \Log::info('AfterCreate record:', $this->record->toArray());
        // Pastikan metode setCalculatedFields() tersedia sebelum dipanggil
        if (method_exists($this->record, 'setCalculatedFields')) {
            $this->record->setCalculatedFields();
            $this->record->save();
        }

        // Pastikan metode getUrl tersedia pada resource
        if (method_exists($this->getResource(), 'getUrl')) {
            $printUrl = $this->getResource()::getUrl('print', ['record' => $this->record]);

            $this->dispatch(
                'open-new-tab',
                url: $printUrl,
                target: '_blank'
            );
        }
    }

 


    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Pembayaran berhasil disimpan';
    }
}
