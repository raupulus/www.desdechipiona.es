@extends('layouts.app')

@section('content')
<header class="bg-gradient-to-br from-slate-950 via-blue-950 to-violet-950 text-white border-b-4 border-orange-700">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <span class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3.5 py-1 text-xs font-extrabold text-amber-300 mb-4">
            {{ __('messages.legal_header_badge') }}
        </span>
        <h1 class="font-serif text-3xl sm:text-5xl font-extrabold text-white mb-4">
            {{ __('messages.legal_header_title') }}
        </h1>
        <p class="text-base sm:text-lg text-slate-200 leading-relaxed">
            {!! __('messages.legal_header_subtitle') !!}
        </p>
    </div>
</header>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">
    {{-- 1. Autoría e Iniciativa Privada --}}
    <section class="rounded-2xl border-2 border-orange-700 bg-white p-6 sm:p-10 shadow-xs space-y-4">
        <h2 class="font-serif text-2xl sm:text-3xl font-extrabold text-slate-950">
            {{ __('messages.legal_s1_title') }}
        </h2>
        <p class="text-base text-slate-900 leading-relaxed">
            {!! __('messages.legal_s1_body') !!}
        </p>
        <div class="rounded-xl border border-slate-200 bg-stone-50 p-5 space-y-2 text-sm text-slate-900">
            <p><strong>{{ __('messages.legal_author_label') }}</strong> Raúl Caro Pastorino</p>
            <p><strong>{{ __('messages.legal_year_label') }}</strong> {{ __('messages.legal_year_value') }}</p>
            <p><strong>{{ __('messages.legal_portfolio_label') }}</strong> <a href="https://raupulus.dev" target="_blank" rel="noopener noreferrer" class="font-extrabold text-blue-900 underline">https://raupulus.dev</a></p>
            <p><strong>{{ __('messages.legal_author_email_label') }}</strong> <a href="mailto:public@raupulus.dev" class="font-extrabold text-blue-900 underline">public@raupulus.dev</a></p>
            <p><strong>{{ __('messages.legal_portal_email_label') }}</strong> <a href="mailto:info@desdechipiona.es" class="font-extrabold text-blue-900 underline">info@desdechipiona.es</a></p>
            <p><strong>{{ __('messages.legal_noreply_label') }}</strong> <code class="font-mono">no-reply@desdechipiona.es</code></p>
            <p><strong>{{ __('messages.legal_telegram_label') }}</strong> <a href="https://t.me/desdechipiona" target="_blank" rel="noopener noreferrer" class="font-extrabold text-blue-900 underline">https://t.me/desdechipiona</a></p>
        </div>
    </section>

    {{-- 2. Metodología: Hechos contrastados vs. mitos y leyendas --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-10 shadow-xs space-y-4">
        <h2 class="font-serif text-2xl sm:text-3xl font-extrabold text-slate-950">
            {{ __('messages.legal_s2_title') }}
        </h2>
        <p class="text-base text-slate-900 leading-relaxed">
            {{ __('messages.legal_s2_body') }}
        </p>
        <ul class="space-y-3 text-sm text-slate-900 list-disc pl-6">
            <li>{!! __('messages.legal_s2_li1') !!}</li>
            <li>{!! __('messages.legal_s2_li2') !!}</li>
            <li>{!! __('messages.legal_s2_li3') !!}</li>
        </ul>
    </section>

    {{-- 3. Propiedad Intelectual del Contenido y Licencia de Difusión --}}
    <section class="rounded-2xl border-2 border-amber-500 bg-white p-6 sm:p-10 shadow-xs space-y-4">
        <h2 class="font-serif text-2xl sm:text-3xl font-extrabold text-slate-950">
            {{ __('messages.legal_s3_title') }}
        </h2>
        <p class="text-base text-slate-900 leading-relaxed">
            {!! __('messages.legal_s3_body') !!}
        </p>
        <div class="rounded-xl border border-amber-300 bg-amber-50/70 p-5 space-y-2 text-sm text-slate-900">
            <p class="font-extrabold text-slate-950">{{ __('messages.legal_s3_license_heading') }}</p>
            <ul class="list-disc pl-5 space-y-1.5">
                <li>{!! __('messages.legal_s3_li1') !!}</li>
                <li>{!! __('messages.legal_s3_li2') !!}</li>
                <li>{!! __('messages.legal_s3_li3') !!}</li>
            </ul>
        </div>
        <p class="text-sm text-slate-700 leading-relaxed pt-2">
            {!! __('messages.legal_s3_code_license') !!}
        </p>
        <div class="flex flex-wrap gap-3 pt-2">
            <a href="{{ route('privacy') }}" class="rounded-xl bg-slate-950 px-5 py-2.5 text-xs font-extrabold text-white hover:bg-orange-800">
                {{ __('messages.legal_btn_privacy') }}
            </a>
            <a href="{{ route('cookies') }}" class="rounded-xl border border-slate-300 bg-stone-50 px-5 py-2.5 text-xs font-extrabold text-slate-950 hover:bg-slate-200">
                {{ __('messages.legal_btn_cookies') }}
            </a>
            <a href="{{ route('contact') }}" class="rounded-xl border border-slate-300 bg-stone-50 px-5 py-2.5 text-xs font-extrabold text-slate-950 hover:bg-slate-200">
                {{ __('messages.legal_btn_contact') }}
            </a>
        </div>
    </section>
</div>
@endsection
