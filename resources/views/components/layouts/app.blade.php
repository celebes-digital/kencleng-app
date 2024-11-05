<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Kencleng App' }}</title>


    @filamentStyles

    @vite(['/resources/css/filament/dashboard/theme.css', '/resources/css/filament/dashboard/theme.css', '/resources/js/app.js'])

    @livewireStyles
</head>

<body>
    <main class="max-w-6xl mx-auto">
        {{ $slot }}
    </main>
</body>
@filamentScripts
@livewireScripts

</html>
