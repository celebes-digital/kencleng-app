<x-filament-panels::form wire:submit="save">

    <div class="flex flex-col md:flex-row w-full items-end">

        {{-- Form --}}
        <div class="w-full md:w-10/12">
            {{ $this->form }}
        </div>

        {{-- Button submit --}}
        <div class="w-full md:w-2/12 flex justify-center md:pl-2 mt-4 md:mt-0">
            <x-filament::button wire:click="save" class="w-full">
                Simpan
            </x-filament::button>
        </div>
    </div>

    
    <div>
        <div>
            <h1 class="font-semibold text-sm text-slate-900">
                Jumlah Distribusi (Sesi Baru): {{ $jumlahDistribusi }} Kencleng
            </h1>
            <p class="text-xs text-slate-600 mt-2">
                Berpindah/Refresh halaman akan mereset jumlah sesi
            </p>
        </div>
    </div>

    <div class="w-full bg-teal-400/20 border border-teal-500 rounded-md text-center py-2">
        <h1 class="text-emerald-800">Distribusi Langsung Donatur</h1>
    </div>

    <div>
        {{ $this->table }}
    </div>


</x-filament-panels::form>