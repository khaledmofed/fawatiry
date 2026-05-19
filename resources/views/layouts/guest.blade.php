<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gradient-to-b from-clay-canvas to-clay-surface-soft font-sans text-clay-body antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10 sm:px-6">
            <a href="/" class="mb-8 flex items-center gap-2 rounded-clay-lg border border-clay-hairline/80 bg-white/70 px-4 py-2 shadow-clay-soft backdrop-blur-md">
                <x-application-logo class="h-10 w-auto fill-current text-clay-ink" />
                <span class="text-sm font-semibold tracking-tight text-clay-ink">{{ config('app.name') }}</span>
            </a>

            <div class="w-full max-w-md overflow-hidden rounded-clay-xl border border-clay-hairline/80 bg-white/85 p-8 shadow-clay-card backdrop-blur-md">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
