
<div class="mt-4 gap-y-8">
    <div class="w-full py-4 bg-teal-400/20 border border-teal-500 rounded-md text-center">
        <h1 class="text-teal-800 font-medium text-lg">Pendaftaran Donatur</h1>
    </div>

    @if (!$this->showForm)
        <div class="mt-4">
            {{ $this->getDataKenclengForm }}
        </div>
    @else
        <div class="my-4">
            {{ $this->getDataDonaturForm }}
        </div>
    @endif
    
    @livewire('notifications')
    <x-filament-actions::modals />
</div>