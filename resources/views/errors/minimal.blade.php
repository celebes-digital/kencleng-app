<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <style>
            body {
                font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            }
        </style>

        @filamentStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">

        <div class="w-full h-screen flex justify-center items-center">
            <div class="bg-teal-500 text-white backdrop-blur-md border border-white lg:p-16 md:p-14 p-8 rounded-3xl space-y-4 flex flex-col items-center text-center">
                <h1 class="font-bold text-6xl">@yield('code')</h1>
                <div class="">
                    <h2 class="font-semibold text-">@yield('message')</h2>
                    <p>@yield('description')</p>
                </div>
                <div>
                    <a href="/" class="bg-white hover:bg-slate-50 py-2 px-4 rounded-md text-teal-600">Back to Aqtif</a>
                    {{-- <button>Hubingi Admin</button> --}}
                </div>
            </div>
        </div>

    </body>
</html>
