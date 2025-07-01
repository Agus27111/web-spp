<x-filament::page>
    <x-filament::card>
        <h2 class="text-xl font-bold mb-4">
            Kelola Siswa untuk Potongan: {{ $this->discount->name }}
        </h2>

        <form wire:submit.prevent="save">
            {{ $this->form }}

            <div class="flex justify-end gap-4 mt-6">
                <x-filament::button type="submit" color="primary" icon="heroicon-o-check" icon-position="after">
                    Simpan Perubahan
                </x-filament::button>
            </div>
        </form>
    </x-filament::card>
</x-filament::page>
