<x-filament-panels::page>

    <x-filament::tabs label="Content tabs">
        <x-filament::tabs.item
            :active="$activeTab === 'Scanner'"
            wire:click="$set('activeTab', 'Scanner')" 
        >
            Scanner
        </x-filament::tabs.item>

        <x-filament::tabs.item 
            :active="$activeTab === 'Camera'"
            wire:click="$set('activeTab', 'Camera')"
        >
            Camera
        </x-filament::tabs.item>

    </x-filament::tabs>

    @if ($activeTab === 'Scanner')

        <div>
            @livewire('forms.scanner-form-field')
        </div>

    @else

        <div>
            @livewire('forms.camera-form-field')
        </div>

    @endif

</x-filament-panels::page>
