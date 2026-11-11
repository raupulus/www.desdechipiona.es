<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{
        fontScale: localStorage.getItem('chipiona_font_scale') || 'normal',
        cookieConsent: localStorage.getItem('chipiona_cookie_consent') || 'pending',
        showCookieBanner: !localStorage.getItem('chipiona_cookie_consent'),
        setFontScale(scale) {
            this.fontScale = scale;
            localStorage.setItem('chipiona_font_scale', scale);
            document.documentElement.setAttribute('data-font-scale', scale);
        },
        setCookieConsent(choice) {
            this.cookieConsent = choice;
            this.showCookieBanner = false;
            localStorage.setItem('chipiona_cookie_consent', choice);
            localStorage.setItem('chipiona_cookie_notice', '1');
            (window.adsbygoogle = window.adsbygoogle || []).requestNonPersonalizedAds = choice === 'all' ? 0 : 1;
            window.dispatchEvent(new CustomEvent('chipiona-consent-updated', { detail: choice }));
        }
    }"
    x-init="document.documentElement.setAttribute('data-font-scale', fontScale)"
    @chipiona-consent-updated.window="cookieConsent = $event.detail; showCookieBanner = false; (window.adsbygoogle = window.adsbygoogle || []).requestNonPersonalizedAds = $event.detail === 'all' ? 0 : 1;"
    @chipiona-open-cookie-banner.window="showCookieBanner = true"
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

        {{-- Banner RGPD de consentimiento de cookies (Aceptar todas / Solo necesarias) --}}
        <div
            x-show="showCookieBanner"
            x-cloak
            role="region"
            aria-label="{{ __('messages.cookie_notice_aria') }}"
            style="bottom: max(1rem, env(safe-area-inset-bottom));"
            class="fixed bottom-4 right-4 left-4 md:left-auto md:max-w-lg z-30 rounded-2xl border-2 border-slate-800 bg-slate-950 p-4 sm:p-5 text-white shadow-2xl"
        >
            <div class="space-y-3">
                <div class="text-xs leading-relaxed text-slate-200">
                    <p class="font-bold text-white mb-1">{{ __('messages.cookies') }}</p>
                    <p>
                        {{ __('messages.cookie_notice_text') }}
                        <a href="{{ route('cookies') }}" class="underline font-semibold text-amber-300 hover:text-amber-200">{{ __('messages.cookie_notice_more') }}</a>.
                    </p>
                </div>
                <div class="flex flex-wrap items-center justify-end gap-2 pt-1">
                    <button
                        type="button"
                        @click="setCookieConsent('essential')"
                        class="rounded-lg border border-slate-600 bg-slate-800 px-3.5 py-2 text-xs font-bold text-slate-100 hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400 cursor-pointer"
                    >
                        {{ __('messages.cookie_notice_essential_only') }}
                    </button>
                    <button
                        type="button"
                        @click="setCookieConsent('all')"
                        class="rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-amber-400 cursor-pointer"
                    >
                        {{ __('messages.cookie_notice_accept') }}
                    </button>
                </div>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
