@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8 space-y-8">
    <h1 class="font-serif text-3xl sm:text-4xl font-extrabold text-slate-950">{{ __('messages.cookies') }}</h1>

    @forelse ($sections as $section)
        <x-section-block :block="$section" />
    @empty
        <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-10 shadow-xs prose-chipiona max-w-none space-y-4">
            <p class="text-sm font-bold text-slate-700">{{ __('messages.cookies_default_text') }}</p>
            <h2>{{ __('messages.cookies_s1_title') }}</h2>
            <p>{!! __('messages.cookies_s1_body') !!}</p>
            <ul>
                <li>{!! __('messages.cookies_s1_li1') !!}</li>
                <li>{!! __('messages.cookies_s1_li2') !!}</li>
                <li>{!! __('messages.cookies_s1_li3') !!}</li>
            </ul>

            <h2>{{ __('messages.cookies_s2_title') }}</h2>
            <p>{!! __('messages.cookies_s2_body') !!}</p>
            <ul>
                <li>{!! __('messages.cookies_s2_li1') !!}</li>
                <li>{!! __('messages.cookies_s2_li2') !!}</li>
                <li>{!! __('messages.cookies_s2_li3') !!}</li>
            </ul>
        </div>
    @endforelse

    {{-- Panel interactivo permanente para consultar, aceptar o revocar el consentimiento de cookies publicitarias --}}
    <section
        id="cookie-consent-panel"
        x-data="{
            consent: localStorage.getItem('chipiona_cookie_consent') || 'pending',
            savedFeedback: false,
            updateChoice(value) {
                this.consent = value;
                this.savedFeedback = true;
                if (typeof this.setCookieConsent === 'function') {
                    this.setCookieConsent(value);
                } else {
                    localStorage.setItem('chipiona_cookie_consent', value);
                    localStorage.setItem('chipiona_cookie_notice', '1');
                    window.dispatchEvent(new CustomEvent('chipiona-consent-updated', { detail: value }));
                }
                setTimeout(() => { this.savedFeedback = false; }, 3500);
            }
        }"
        @chipiona-consent-updated.window="consent = $event.detail"
        class="rounded-2xl border-2 border-orange-700 bg-white p-6 sm:p-8 shadow-xs space-y-4"
    >
        <h2 class="font-serif text-xl sm:text-2xl font-extrabold text-slate-950">
            {{ __('messages.cookies_s3_title') }}
        </h2>
        <p class="text-sm text-slate-800 leading-relaxed">
            {{ __('messages.cookies_s3_body') }}
        </p>

        <div class="rounded-xl border border-slate-200 bg-stone-50 p-4 text-xs sm:text-sm text-slate-900">
            <span class="font-extrabold text-slate-950">{{ __('messages.cookies_consent_status_label') }}</span>
            <span x-show="consent === 'all'" class="ml-1 font-bold text-emerald-800">{{ __('messages.cookies_consent_status_all') }}</span>
            <span x-show="consent === 'essential'" class="ml-1 font-bold text-slate-800">{{ __('messages.cookies_consent_status_essential') }}</span>
            <span x-show="consent !== 'all' && consent !== 'essential'" class="ml-1 font-bold text-amber-800">{{ __('messages.cookies_consent_status_pending') }}</span>
        </div>

        <div class="flex flex-wrap items-center gap-3 pt-1">
            <button
                type="button"
                @click="updateChoice('all')"
                class="rounded-xl bg-orange-700 px-5 py-2.5 text-xs sm:text-sm font-extrabold text-white hover:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-amber-400 cursor-pointer"
            >
                {{ __('messages.cookies_consent_btn_accept_all') }}
            </button>
            <button
                type="button"
                @click="updateChoice('essential')"
                class="rounded-xl border border-slate-300 bg-stone-100 px-5 py-2.5 text-xs sm:text-sm font-extrabold text-slate-950 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-400 cursor-pointer"
            >
                {{ __('messages.cookies_consent_btn_reject_ads') }}
            </button>
        </div>

        <p x-show="savedFeedback" x-cloak class="text-xs font-extrabold text-emerald-800">
            ✓ {{ __('messages.cookies_consent_saved_feedback') }}
        </p>
    </section>
</div>
@endsection
