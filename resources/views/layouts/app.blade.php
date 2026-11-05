<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{
        fontScale: localStorage.getItem('chipiona_font_scale') || 'normal',
        setFontScale(scale) {
            this.fontScale = scale;
            localStorage.setItem('chipiona_font_scale', scale);
            document.documentElement.setAttribute('data-font-scale', scale);
        }
    }"
    x-init="document.documentElement.setAttribute('data-font-scale', fontScale)"
>
    <head>
        <script>document.documentElement.setAttribute('data-font-scale', localStorage.getItem('chipiona_font_scale') || 'normal');</script>
        @include('layouts.head')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('head_css')
        @stack('head_js')
    </head>
    <body class="min-h-screen overflow-x-hidden bg-stone-50 text-slate-950 antialiased flex flex-col selection:bg-orange-200 selection:text-slate-950">
        <a
            href="#main-content"
            class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2.5 focus:bg-slate-950 focus:text-white focus:font-bold focus:rounded-lg focus:shadow-lg focus:outline-none"
        >
            {{ __('messages.skip_to_content') }}
        </a>

        @include('layouts.navbar')

        <x-flash-messages />

        @yield('header')

        <main id="main-content" tabindex="-1" class="flex-1 focus:outline-none">
            @yield('content')
        </main>

        @include('layouts.footer')

        {{-- Aviso informativo de cookies técnicas de primera parte (no intrusivo) --}}
        <div
            x-data="{ dismissed: localStorage.getItem('chipiona_cookie_notice') === '1' }"
            x-show="!dismissed"
            x-cloak
            role="region"
            aria-label="{{ __('messages.cookie_notice_aria') }}"
            style="bottom: max(1rem, env(safe-area-inset-bottom));"
            class="fixed bottom-4 right-4 left-4 md:left-auto md:max-w-md z-30 rounded-2xl border-2 border-slate-800 bg-slate-950 p-4 text-white shadow-2xl"
        >
            <div class="flex items-start justify-between gap-3">
                <div class="text-xs leading-relaxed text-slate-200">
                    <p class="font-bold text-white mb-1">{{ __('messages.cookies') }}</p>
                    <p>
                        {{ __('messages.cookie_notice_text') }}
                        <a href="{{ route('cookies') }}" class="underline font-semibold text-amber-300 hover:text-amber-200">{{ __('messages.cookie_notice_more') }}</a>.
                    </p>
                </div>
                <button
                    type="button"
                    @click="localStorage.setItem('chipiona_cookie_notice', '1'); dismissed = true"
                    class="shrink-0 rounded-lg bg-orange-700 px-3 py-1.5 text-xs font-bold text-white hover:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-amber-400"
                >
                    {{ __('messages.cookie_notice_accept') }}
                </button>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
