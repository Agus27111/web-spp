<?php

namespace App\Filament\Admin\Resources\PaymentResource\Pages;

use App\Filament\Admin\Resources\PaymentResource;
use Filament\Resources\Pages\Page;
use Torgodly\Html2Media\Actions\Html2MediaAction;

class PrintPayment extends Page
{
    protected static string $resource = PaymentResource::class;
    protected static string $view = 'prints.payment';

    public $record;

    public function mount($record)
    {
        $this->record = \App\Models\Payment::findOrFail($record);
    }

    public function getHeaderActions(): array
    {
        return [
            Html2MediaAction::make('print')
                ->label('Cetak Sekarang')
                ->icon('heroicon-o-printer')
                ->scale(1.2)
                ->print()
                ->preview()
                ->filename('struk_' . $this->record->id)
                ->savePdf()
                ->orientation('portrait')
                ->format('a4')
                ->margin([10, 15, 10, 15])
                ->content(view('prints.payment', ['record' => $this->record]))
        ];
    }
}
