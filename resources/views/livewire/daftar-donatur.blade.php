<div class="w-full my-4 md:my-8">
    <div class="w-full h-24 bg-gradient-to-br from-emerald-800 to-emerald-700 rounded-md flex justify-center items-center">
        <h1 class="text-lg font-semibold text-white">Formulir Pendaftaran</h1>
    </div>
    <form wire:submit="create">
        {{ $this->form }}
    </form>
</div>
