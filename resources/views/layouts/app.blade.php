<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="min-h-full bg-clay-canvas font-sans text-clay-body antialiased" x-data="{ navOpen: false }">
        <div
            x-show="navOpen"
            x-transition.opacity
            class="fixed inset-0 z-40 bg-clay-primary/20 backdrop-blur-sm lg:hidden"
            @click="navOpen = false"
            style="display: none;"
        ></div>

        @include('layouts.partials.clay-sidebar')

        <div class="min-h-screen lg:ps-72">
            @include('layouts.partials.clay-topbar')

            @isset($header)
                <header class="border-b border-clay-hairline/70 bg-clay-canvas/80 backdrop-blur-md">
                    <div class="mx-auto max-w-[1280px] px-4 py-5 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="mx-auto max-w-[1280px] px-4 py-8 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
