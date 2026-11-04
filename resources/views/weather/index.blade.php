@extends('layouts.app')

@section('content')
@php
    $weather = $summary['weather'] ?? [];
    $tides = $summary['tides'] ?? [];
@endphp

<header class="bg-gradient-to-br from-slate-950 via-blue-950 to-violet-950 text-white border-b-4 border-orange-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <span class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3.5 py-1 text-xs font-extrabold text-amber-300 mb-4">
            🌊 {{ $weather['source'] ?? 'AEMET Municipio 11016 (Chipiona)' }}
        </span>
        <h1 class="font-serif text-3xl sm:text-5xl font-extrabold text-white mb-4">
            {{ __('messages.weather_header_title') }}
        </h1>
        <p class="max-w-3xl text-base sm:text-lg text-slate-200 leading-relaxed">
            {{ __('messages.weather_header_subtitle') }}
        </p>
    </div>
</header>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
    {{-- Paneles de meteorología AEMET y Coeficiente de Marea --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Estado del cielo y temperaturas AEMET --}}
        <section class="lg:col-span-5 rounded-2xl border-2 border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-6">
            <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                <div>
                    <h2 class="font-serif text-2xl font-extrabold text-slate-950">{{ __('messages.weather_today_title') }}</h2>
                    <p class="text-xs font-bold text-slate-700">{{ __('messages.weather_updated', ['date' => $summary['updated_at'] ?? '']) }}</p>
                </div>
                <span class="rounded-xl bg-blue-950 px-3 py-1.5 font-mono text-xs font-extrabold text-amber-300">AEMET 11016</span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="rounded-xl border border-orange-200 bg-orange-50 p-4">
                    <span class="block text-xs font-extrabold uppercase text-orange-950">{{ __('messages.weather_temp_max') }}</span>
                    <span class="font-serif text-3xl font-extrabold text-slate-950">{{ $weather['temp_max'] ?? 22 }} °C</span>
                </div>
                <div class="rounded-xl border border-sky-200 bg-sky-50 p-4">
                    <span class="block text-xs font-extrabold uppercase text-sky-950">{{ __('messages.weather_temp_min') }}</span>
                    <span class="font-serif text-3xl font-extrabold text-slate-950">{{ $weather['temp_min'] ?? 15 }} °C</span>
                </div>
            </div>

            <dl class="space-y-3 text-sm border-t border-slate-200 pt-4">
                <div class="flex justify-between">
                    <dt class="font-bold text-slate-700">{{ __('messages.weather_sky_state') }}</dt>
                    <dd class="font-extrabold text-slate-950">{{ $weather['sky_state'] ?? 'Despejado' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="font-bold text-slate-700">{{ __('messages.weather_wind') }}</dt>
                    <dd class="font-extrabold text-slate-950">{{ $weather['wind_direction'] ?? 'Poniente' }} ({{ $weather['wind_speed_kmh'] ?? 15 }} km/h)</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="font-bold text-slate-700">{{ __('messages.weather_rain_prob') }}</dt>
                    <dd class="font-extrabold text-slate-950">{{ $weather['rain_prob'] ?? 5 }} %</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="font-bold text-slate-700">{{ __('messages.weather_uv') }}</dt>
                    <dd class="font-extrabold text-slate-950">Nivel {{ $weather['uv_max'] ?? 6 }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="font-bold text-slate-700">Temperatura del agua (Atlántico):</dt>
                    <dd class="font-extrabold text-blue-950">~{{ $weather['sea_temp'] ?? 19 }} °C</dd>
                </div>
            </dl>
        </section>

        {{-- Tabla de Mareas y Corrales de Pesca --}}
        <section class="lg:col-span-7 rounded-2xl border-2 border-blue-900 bg-white p-6 sm:p-8 shadow-xs space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 pb-4">
                <div>
                    <h2 class="font-serif text-2xl font-extrabold text-slate-950">{{ __('messages.weather_tides_title') }}</h2>
                    <p class="text-sm font-bold text-blue-950 mt-1">{{ $tides['type_label'] ?? '' }}</p>
                </div>
                <div class="rounded-xl bg-slate-950 px-4 py-2 text-center">
                    <span class="block text-xs font-extrabold uppercase tracking-wider text-amber-300">{{ __('messages.weather_tides_coef') }}</span>
                    <span class="font-mono text-2xl font-extrabold text-white">{{ $tides['coefficient'] ?? 72 }}</span>
                </div>
            </div>

            <div class="overflow-x-auto" tabindex="0" role="region" aria-label="Tabla de mareas y estado de los corrales de pesca">
                <table class="w-full text-left border-collapse">
                    <caption class="sr-only">Horarios diarios de pleamar y bajamar, altura en metros y estado de los corrales de pesca de Chipiona</caption>
                    <thead>
                        <tr class="border-b-2 border-slate-300 text-xs font-extrabold uppercase text-slate-800">
                            <th scope="col" class="py-3 pr-4">{{ __('messages.tides_col_tide') }}</th>
                            <th scope="col" class="py-3 px-4">{{ __('messages.tides_col_time') }}</th>
                            <th scope="col" class="py-3 px-4">{{ __('messages.tides_col_height') }}</th>
                            <th scope="col" class="py-3 pl-4">{{ __('messages.tides_col_state') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm">
                        @foreach($tides['events'] ?? [] as $event)
                            <tr>
                                <td class="py-3.5 pr-4 font-extrabold {{ $event['type'] === 'bajamar' ? 'text-emerald-950' : 'text-blue-950' }}">
                                    {{ $event['type'] === 'bajamar' ? '⬇️ ' : '⬆️ ' }}{{ $event['label'] }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-extrabold text-slate-950">{{ $event['time'] }} h</td>
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-800">{{ number_format((float) $event['height_m'], 2) }} m</td>
                                <td class="py-3.5 pl-4 text-slate-800">{{ $event['corrales_note'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="rounded-xl border border-amber-400 bg-amber-50 p-4 text-xs sm:text-sm text-slate-950 leading-relaxed">
                <strong>🦎 Consejo de conservación de los Corrales de Pesca:</strong>
                Para contemplar las paredes de piedra ostionera de los corrales (Las Canteras, Montijo, La Longuera), acude siempre en torno a la <strong>bajamar</strong>. No camines nunca sobre el muro recubierto de ostiones y escaramujo para proteger el ecosistema marino.
            </div>
        </section>
    </div>

    @if($coastalPages->count() > 0)
        <section class="space-y-4">
            <h2 class="font-serif text-2xl font-extrabold text-slate-950">🏖️ {{ __('messages.coastal_linked_heading') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($coastalPages as $coastal)
                    <a href="{{ route('pages.show', $coastal->slug) }}" class="rounded-2xl border border-slate-200 bg-white p-5 hover:border-orange-600 transition block">
                        <span class="text-xs font-extrabold uppercase text-orange-800">{{ $coastal->content_group?->label() }}</span>
                        <h3 class="font-serif text-base font-extrabold text-slate-950 mt-1">
                            {{ $coastal->getTranslation('title', app()->getLocale()) }}
                        </h3>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
