<div class="flex space-x-2 items-center">
    <div class="font-medium text-teal-800 py-1 px-3 text-sm rounded-t-lg">
        @php
            $user = auth()->user()
        @endphp
        {{ ucwords(
            $user->is_admin 
                ? $user->admin?->nama
                : $user->profile?->nama
            )
        }}
        <span class="block text-slate-400 text-[10px]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[10px] inline-block">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
            </svg>
            {{ ucwords($user->is_admin ? $user->admin->level : $user->profile->group) }}
        </span>
    </div>
    <x-filament::icon-button
        icon="heroicon-o-calendar"
        href="/kalender-koleksi"
        tag="a"
        tooltip="Jadwal Koleksi"
        wire:navigate
        class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700"
    />
</div>