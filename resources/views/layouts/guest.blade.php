<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ setting('site_title', config('app.name', 'Laravel')) }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-950 antialiased bg-stone-50">
        <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-slate-950 focus:text-white focus:rounded-lg focus:shadow-lg focus:outline-none">
            {{ __('messages.skip_to_content') }}
        </a>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-stone-50">
            <div>
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2" aria-label="{{ setting('site_title', config('app.name', 'Desde Chipiona')) }} — {{ __('messages.home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ setting('site_title', config('app.name', 'Desde Chipiona')) }}" width="64" height="64" class="w-16 h-16 rounded-full border-2 border-amber-500 shadow-sm object-cover" />
                    <span class="font-serif text-lg font-extrabold text-slate-950">{{ setting('site_title', config('app.name', 'Desde Chipiona')) }}</span>
                </a>
            </div>

            <main id="main-content" tabindex="-1" class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white border-2 border-slate-200 border-t-4 border-t-orange-700 shadow-md overflow-hidden sm:rounded-2xl focus:outline-none">
                {{ $slot }}
            </main>
        </div>
        @stack('scripts')
    </body>
</html>
