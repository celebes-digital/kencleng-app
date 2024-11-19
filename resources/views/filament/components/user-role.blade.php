<div class="font-medium text-teal-800 py-2 px-3 text-sm rounded-t-lg">
    {{ 
        auth()->user()->profile?->nama 
        . (
            auth()->user()->is_admin 
            ? 'Admin' 
            : ' (' . ucwords(auth()->user()->profile?->group . ')'
        )) 
    }}
</div>