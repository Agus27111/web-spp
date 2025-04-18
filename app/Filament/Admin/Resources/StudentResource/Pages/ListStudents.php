<?php

namespace App\Filament\Admin\Resources\StudentResource\Pages;

use App\Filament\Admin\Resources\StudentResource;
use App\Imports\StudentsImport;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Actions\Imports\Events\ImportStarted;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    /**
     * @var \Livewire\TemporaryUploadedFile|null
     */
    public $file;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getHeader(): ?View
    {
        $data = Actions\CreateAction::make();
        return view('fillament.custom.upload-file', ['data' => $data, 'file' => $this->file]);
    }



    public function save()
    {
        $this->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        Excel::import(new StudentsImport, $this->file->getRealPath());

        Notification::make()
            ->title('Import Berhasil')
            ->success()
            ->body('Data siswa telah berhasil diimpor dari file.')
            ->send();

        $this->file = null;
    }
}
