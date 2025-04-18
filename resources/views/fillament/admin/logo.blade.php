@auth
    @if (auth()->user()->foundation && auth()->user()->foundation->image)
        <img src="{{ asset('storage/' . auth()->user()->foundation->image) }}"
            alt="{{ auth()->user()->foundation->name ?? 'Logo' }}" style="max-height: 30px;">
    @else
        {{-- Tampilkan logo default dari favicon jika user punya foundation tapi tidak ada gambar --}}
        <img src="{{ asset('favicon.ico') }}" alt="Logo Perusahaan" style="max-height: 30px;">
    @endif
@else
    {{-- Tampilkan logo default dari favicon jika tidak ada user yang login --}}
    <img src="{{ asset('favicon.ico') }}" alt="Logo Default" style="max-height: 30px;">
@endauth
