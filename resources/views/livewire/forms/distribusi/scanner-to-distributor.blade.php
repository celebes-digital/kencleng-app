<x-filament-panels::form wire:submit="save">

    <div class="flex w-full items-end gap-4">

        {{-- Form --}}
        <div class="w-4/5">
            {{ $this->form }}
        </div>

        {{-- Button submit --}}
        <div class="w-1/5">
            <x-filament::button wire:click.="save">
                Tambahkan
            </x-filament::button>
        </div>
    </div>

    <div>
        <div>
            <h1 class="font-semibold text-lg text-slate-900">Sesi baru</h1>
            <p class="text-base text-slate-600">Berpindah halaman akan mereset data sesi</p>
        </div>
        <div class="space-y-2 mt-4">
            @foreach ($newDistribusi as $data)
                <div class="w-fit py-2 px-4 bg-teal-200/20 border border-teal-400 flex divide-x space-x-8 rounded-md text-teal-700">
                    <p>{{ $data['no_kencleng'] }}</p>
                    <p>{{ $data['status'] }}</p>
                    <p>{{ $data['batch'] }}</p>
                    <p>{{ \Carbon\Carbon::parse($data['waktu_distribusi'])->format('d F Y H:i') }}</p>
                </div>
            @endforeach
        </div>
    </div>


</x-filament-panels::form>