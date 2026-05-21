<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#4f46e5">
        <link rel="manifest" href="/manifest.json">
        <link rel="icon" href="/icons/icon-192.svg">

        <title>{{ config('app.name', 'VocabMaster Pro') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-8"
             style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="w-full max-w-md">
                <a href="/" wire:navigate class="flex items-center justify-center gap-3 mb-8">
                    <x-application-logo class="w-10 h-10" />
                    <span class="text-2xl font-bold text-white">VocabMaster</span>
                </a>

                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-8">
                    {{ $slot }}
                </div>

                <p class="text-center mt-6 text-sm text-white/70">
                    &copy; {{ date('Y') }} VocabMaster Pro. All rights reserved.
                </p>
            </div>
        </div>
    </body>
</html>
