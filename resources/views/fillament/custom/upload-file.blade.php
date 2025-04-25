<x-filament::breadcrumbs :breadcrumbs="[
    '/admin/students' => 'Students',
    '' => 'List',
]" />

<div class="flex justify-between mt-1">
    <div class="font-bold text-3xl">
        Students
    </div>
    <div>
        {{ $data }}
    </div>
</div>

{{-- Form Upload CSV --}}
<div wire:submit.prevent="save" class="space-y-4">
    <form wire:submit.prevent="save" enctype="multipart/form-data">
        {{-- Pilihan Foundation untuk Superadmin --}}
        @if (auth()->user()->role === 'superadmin')
            <div class="mb-4">
                <label for="foundation" class="block text-sm font-medium text-gray-700">Foundation</label>
                <select id="foundation" wire:model="selectedFoundation" class="block w-full mt-1 text-sm text-gray-500">
                    <option value="">-- Select Foundation --</option>
                    @foreach ($foundations as $foundation)
                        <option value="{{ $foundation->id }}">{{ $foundation->name }}</option>
                    @endforeach
                </select>
                @error('selectedFoundation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        @endif

        {{-- File Input --}}
        <input type="file" wire:model="file"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
            file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700
            hover:file:bg-blue-100" />

        @if ($file)
            <div class="text-green-600 font-medium flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                File berhasil dipilih
            </div>
        @endif

        {{-- Tombol Upload --}}
        <button type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
            wire:loading.attr="disabled">
            <span wire:loading.remove>Upload</span>
            <svg wire:loading class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
        </button>
    </form>
</div>
