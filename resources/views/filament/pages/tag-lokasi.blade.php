<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getFormActions()" 
        />
    </x-filament-panels::form>
</x-filament-panels::page>

@script
<script>
    const successGetLocation = (position) => {
        $wire.set('data.latitude', position.coords.latitude)
        $wire.set('data.longitude', position.coords.longitude)
    }

    const error = () => {
        console.log('Error to get location');
    }

    navigator
        .geolocation
        .getCurrentPosition(
            successGetLocation,
            error
        );
</script>
@endscript