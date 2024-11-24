<div class="w-full flex flex-col justify-center items-center mt-4">
    <div class="w-full rounded-md bg-teal-400/10 border border-teal-500 py-4 flex flex-col items-center gap-4">
        <h3 class="text-lg text-teal-700 text-center font-semibold">
            Pindai QR
        </h3>
        <img src="{{ $qr }}" alt="QR Scanner" class="rounded-lg border border-teal-500">
        <div class="space-y-2">
            <h4 class="text-lg text-teal-700 text-center font-semibold">{{ $cabang }}</h4>
            <p class="text-lg text-teal-500 text-center font-semibold border py-1 px-2 rounded-md border-teal-500">Pindai untuk menjadi {{ $title }}</p>
        </div>
    </div>
</div>