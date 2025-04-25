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
use App\Models\Foundation;
use Illuminate\Support\Facades\Auth;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    /**
     * @var \Livewire\TemporaryUploadedFile|null
     */
    public $file;
    public $selectedFoundation;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getHeader(): ?View
    {
        $foundations = Foundation::all();
        return view('fillament.custom.upload-file', [
            'data' => Actions\CreateAction::make(),
            'file' => $this->file,
            'foundations' => $foundations,
        ]);
    }



    public function save()
    {
        $this->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
            // Jika superadmin, kita pastikan foundation dipilih
            'selectedFoundation' => Auth::user()->role === 'superadmin' ? 'required' : '',
        ]);

        $foundationId = Auth::user()->role === 'superadmin'
            ? $this->selectedFoundation
            : Auth::user()->foundation_id;

        Excel::import(new StudentsImport($foundationId), $this->file->getRealPath());

        Notification::make()
            ->title('Saved successfully')
            ->body('Data siswa telah berhasil diimpor dari file.')
            ->success()
            ->send();

        $this->file = null;
    }
}
