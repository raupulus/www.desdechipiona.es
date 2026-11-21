@extends('layouts.app')

@section('content')
<header class="bg-gradient-to-br from-slate-950 via-blue-950 to-violet-950 text-white border-b-4 border-orange-700">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <span class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3.5 py-1 text-xs font-extrabold text-amber-300 mb-4">
            {{ __('messages.about_header_badge') }}
        </span>
        <h1 class="font-serif text-3xl sm:text-5xl font-extrabold text-white mb-4">
            {{ __('messages.about_title') }}
        </h1>
        <p class="text-base sm:text-lg text-slate-200 leading-relaxed max-w-3xl">
            {{ __('messages.about_header_subtitle') }}
        </p>
    </div>
</header>

<div class="max-w-5xl mx-auto px-4 py-12 sm:px-6 lg:px-8 space-y-10">
    @forelse($sections as $section)
        <x-section-block :block="$section" />
    @empty
        {{-- 1. Origen y trayectoria desde 2008 --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-10 shadow-xs space-y-4 prose-chipiona max-w-none">
            <h2 class="font-serif text-2xl sm:text-3xl font-extrabold text-slate-950">
                {{ __('messages.about_origin_title') }}
            </h2>
            <p>{!! __('messages.about_p1') !!}</p>
            <p>{!! __('messages.about_p2') !!}</p>
            <p>{{ __('messages.about_p3') }}</p>
            <p>{{ __('messages.about_origin_p4') }}</p>
        </section>

        {{-- 2. Metodología documental y niveles de evidencia --}}
        <section class="rounded-2xl border-2 border-slate-200 bg-white p-6 sm:p-10 shadow-xs space-y-6">
            <div>
                <h2 class="font-serif text-2xl sm:text-3xl font-extrabold text-slate-950">
                    {{ __('messages.about_methodology_title') }}
                </h2>
                <p class="mt-2 text-base text-slate-800 leading-relaxed">
                    {{ __('messages.about_methodology_intro') }}
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="rounded-xl border-2 border-sky-800 bg-sky-50 p-5 space-y-1.5">
                    <h3 class="font-serif text-base font-extrabold text-sky-950">⚖️ {{ __('messages.about_ev_consolidated_title') }}</h3>
                    <p class="text-sm text-sky-950/90 leading-relaxed">{{ __('messages.about_ev_consolidated_desc') }}</p>
                </div>
                <div class="rounded-xl border-2 border-violet-800 bg-violet-50 p-5 space-y-1.5">
                    <h3 class="font-serif text-base font-extrabold text-violet-950">📜 {{ __('messages.about_ev_documented_title') }}</h3>
                    <p class="text-sm text-violet-950/90 leading-relaxed">{{ __('messages.about_ev_documented_desc') }}</p>
                </div>
                <div class="rounded-xl border-2 border-amber-800 bg-amber-50 p-5 space-y-1.5">
                    <h3 class="font-serif text-base font-extrabold text-amber-950">🗣️ {{ __('messages.about_ev_tradition_title') }}</h3>
                    <p class="text-sm text-amber-950/90 leading-relaxed">{{ __('messages.about_ev_tradition_desc') }}</p>
                </div>
                <div class="rounded-xl border-2 border-rose-800 bg-rose-50 p-5 space-y-1.5">
                    <h3 class="font-serif text-base font-extrabold text-rose-950">🔎 {{ __('messages.about_ev_discutido_title') }}</h3>
                    <p class="text-sm text-rose-950/90 leading-relaxed">{{ __('messages.about_ev_discutido_desc') }}</p>
                </div>
            </div>
        </section>

        {{-- 3. Archivos y fuentes oficiales consultadas --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-10 shadow-xs space-y-4">
            <h2 class="font-serif text-2xl sm:text-3xl font-extrabold text-slate-950">
                {{ __('messages.about_sources_title') }}
            </h2>
            <p class="text-base text-slate-800 leading-relaxed">
                {{ __('messages.about_sources_intro') }}
            </p>
            <ul class="list-disc pl-6 space-y-2.5 text-sm sm:text-base text-slate-800 leading-relaxed">
                <li>{!! __('messages.about_sources_li1') !!}</li>
                <li>{!! __('messages.about_sources_li2') !!}</li>
                <li>{!! __('messages.about_sources_li3') !!}</li>
                <li>{!! __('messages.about_sources_li4') !!}</li>
            </ul>
        </section>

        {{-- 4. Mapa temático del portal --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-10 shadow-xs space-y-6">
            <div>
                <h2 class="font-serif text-2xl sm:text-3xl font-extrabold text-slate-950">
                    {{ __('messages.about_map_title') }}
                </h2>
                <p class="mt-2 text-base text-slate-800 leading-relaxed">
                    {{ __('messages.about_map_intro') }}
                </p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('home') }}" class="block rounded-xl border border-slate-200 bg-stone-50 p-5 hover:border-orange-700 hover:bg-orange-50/40 transition">
                    <h3 class="font-serif text-lg font-extrabold text-slate-950">🏛️ {{ __('messages.about_map_pages_title') }}</h3>
                    <p class="mt-1.5 text-sm text-slate-700 leading-relaxed">{{ __('messages.about_map_pages_desc') }}</p>
                </a>
                <a href="{{ route('news.index') }}" class="block rounded-xl border border-slate-200 bg-stone-50 p-5 hover:border-orange-700 hover:bg-orange-50/40 transition">
                    <h3 class="font-serif text-lg font-extrabold text-slate-950">📰 {{ __('messages.about_map_news_title') }}</h3>
                    <p class="mt-1.5 text-sm text-slate-700 leading-relaxed">{{ __('messages.about_map_news_desc') }}</p>
                </a>
                <a href="{{ route('proverbs.index') }}" class="block rounded-xl border border-slate-200 bg-stone-50 p-5 hover:border-orange-700 hover:bg-orange-50/40 transition">
                    <h3 class="font-serif text-lg font-extrabold text-slate-950">🗣️ {{ __('messages.about_map_proverbs_title') }}</h3>
                    <p class="mt-1.5 text-sm text-slate-700 leading-relaxed">{{ __('messages.about_map_proverbs_desc') }}</p>
                </a>
                <a href="{{ route('weather.index') }}" class="block rounded-xl border border-slate-200 bg-stone-50 p-5 hover:border-orange-700 hover:bg-orange-50/40 transition">
                    <h3 class="font-serif text-lg font-extrabold text-slate-950">🌊 {{ __('messages.about_map_utilities_title') }}</h3>
                    <p class="mt-1.5 text-sm text-slate-700 leading-relaxed">{{ __('messages.about_map_utilities_desc') }}</p>
                </a>
            </div>
        </section>

        {{-- 5. Autoría, independencia y participación --}}
        <section class="rounded-2xl border-2 border-orange-700 bg-white p-6 sm:p-10 shadow-xs space-y-5">
            <h2 class="font-serif text-2xl sm:text-3xl font-extrabold text-slate-950">
                {{ __('messages.about_authorship_title') }}
            </h2>
            <p class="text-base text-slate-800 leading-relaxed">{!! __('messages.about_authorship_p1') !!}</p>
            <p class="text-base text-slate-800 leading-relaxed">{{ __('messages.about_authorship_p2') }}</p>
            <div class="flex flex-wrap gap-3 pt-2">
                <a href="{{ route('home') }}" class="rounded-xl bg-orange-700 px-5 py-2.5 text-sm font-extrabold text-white hover:bg-orange-800 transition">
                    {{ __('messages.about_cta_explore') }}
                </a>
                <a href="{{ route('suggestions.create') }}" class="rounded-xl bg-slate-950 px-5 py-2.5 text-sm font-extrabold text-white hover:bg-slate-800 transition">
                    {{ __('messages.about_cta_suggest') }}
                </a>
                <a href="{{ route('legal') }}" class="rounded-xl border border-slate-300 bg-stone-50 px-5 py-2.5 text-sm font-extrabold text-slate-950 hover:bg-slate-200 transition">
                    {{ __('messages.about_cta_legal') }}
                </a>
                <a href="{{ route('contact') }}" class="rounded-xl border border-slate-300 bg-stone-50 px-5 py-2.5 text-sm font-extrabold text-slate-950 hover:bg-slate-200 transition">
                    {{ __('messages.about_cta_contact') }}
                </a>
            </div>
        </section>
    @endforelse
</div>
@endsection
