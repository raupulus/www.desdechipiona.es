@extends('layouts.app')

@section('content')
@php
    $categories = [
        'all' => __('messages.phones_cat_all'),
        'emergencias' => __('messages.phones_cat_emergencias'),
        'salud' => __('messages.phones_cat_salud'),
        'ayuntamiento' => __('messages.phones_cat_ayuntamiento'),
        'transporte' => __('messages.phones_cat_transporte'),
        'cultura' => __('messages.phones_cat_cultura'),
    ];
@endphp

<header class="bg-gradient-to-br from-slate-950 via-blue-950 to-violet-950 text-white border-b-4 border-orange-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <span class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3.5 py-1 text-xs font-extrabold text-amber-300 mb-4">
            {{ __('messages.phones_header_badge') }}
        </span>
        <h1 class="font-serif text-3xl sm:text-5xl font-extrabold text-white mb-4">
            {{ __('messages.phones_header_title') }}
        </h1>
        <p class="max-w-3xl text-base sm:text-lg text-slate-200 leading-relaxed">
            {{ __('messages.phones_header_subtitle') }}
        </p>

        <form action="{{ route('phones.index') }}" method="GET" role="search" aria-label="Buscar teléfonos oficiales" class="mt-6 max-w-2xl flex flex-col sm:flex-row gap-3">
            @if($category !== '' && $category !== 'all')
                <input type="hidden" name="category" value="{{ $category }}">
            @endif
            <label for="phones-search-q" class="sr-only">{{ __('messages.search') }}</label>
            <input
                type="search"
                id="phones-search-q"
                name="q"
                value="{{ $search ?? '' }}"
                placeholder="{{ __('messages.phones_search_placeholder') }}"
                class="flex-1 rounded-xl border-2 border-slate-300 bg-white px-4 py-2.5 text-base sm:text-sm font-bold text-slate-950 placeholder:text-slate-600"
            >
            <button type="submit" class="rounded-xl bg-orange-700 px-6 py-2.5 text-sm font-extrabold text-white hover:bg-orange-800 cursor-pointer">
                {{ __('messages.proverbs_search_btn') }}
            </button>
            @if(($search ?? '') !== '' || ($category !== '' && $category !== 'all'))
                <a href="{{ route('phones.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-500 bg-slate-900 px-4 py-2.5 text-xs font-extrabold text-amber-300 hover:bg-slate-800">
                    {{ __('messages.proverbs_clear_btn') }}
                </a>
            @endif
        </form>

        <div class="mt-5 flex flex-wrap gap-2">
            @foreach($categories as $catKey => $catLabel)
                <a
                    href="{{ route('phones.index', ['category' => $catKey === 'all' ? null : $catKey, 'q' => ($search ?? '') ?: null]) }}"
                    class="rounded-xl px-4 py-2 text-xs sm:text-sm font-extrabold transition {{ ($category === $catKey || ($category === '' && $catKey === 'all')) ? 'bg-amber-400 text-slate-950' : 'bg-slate-900/90 text-white border border-slate-700 hover:bg-slate-800' }}"
                >
                    {{ $catLabel }}
                </a>
            @endforeach
        </div>
    </div>
</header>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">
    @if($phones->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($phones as $phone)
                <article class="flex flex-col justify-between rounded-2xl border-2 {{ $phone->is_emergency ? 'border-rose-700 bg-rose-50/40' : 'border-slate-200 bg-white' }} p-6 shadow-xs">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="rounded-lg px-2.5 py-1 text-xs font-extrabold uppercase {{ $phone->is_emergency ? 'bg-rose-800 text-white' : 'bg-slate-900 text-amber-300' }}">
                                {{ $phone->is_emergency ? __('messages.emergency_24h_badge') : ($categories[$phone->category] ?? $phone->category) }}
                            </span>
                            @if($phone->schedule)
                                <span class="text-xs font-bold text-slate-700">🕒 {{ $phone->schedule }}</span>
                            @endif
                        </div>

                        <h2 class="font-serif text-xl font-extrabold text-slate-950">{{ $phone->name }}</h2>

                        @if($phone->description)
                            <p class="text-sm text-slate-800 leading-relaxed">{{ $phone->description }}</p>
                        @endif

                        @if($phone->address)
                            <p class="text-xs font-bold text-slate-800">📍 {{ $phone->address }}</p>
                        @endif
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-200 space-y-2">
                        <a
                            href="tel:{{ $phone->tel_href }}"
                            class="flex items-center justify-center gap-2 w-full rounded-xl {{ $phone->is_emergency ? 'bg-rose-800 hover:bg-rose-900' : 'bg-slate-950 hover:bg-orange-800' }} px-4 py-3 text-base font-extrabold text-white transition"
                        >
                            <span>📞 {{ __('messages.call_phone_prefix') }} {{ $phone->phone }}</span>
                        </a>
                        @if($phone->secondary_phone)
                            <p class="text-center text-xs font-bold text-slate-800">
                                {{ __('messages.alt_phone_label') }} <strong>{{ $phone->secondary_phone }}</strong>
                            </p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center">
            <p class="text-base font-extrabold text-slate-950">{{ __('messages.no_phones_found') }}</p>
        </div>
    @endif
</div>
@endsection
