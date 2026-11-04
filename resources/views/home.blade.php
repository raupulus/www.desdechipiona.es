@extends('layouts.app')

@section('content')
@php
    $activeGroup = (string) ($filters['group'] ?? 'all');
    $activeEra = (string) ($filters['era'] ?? 'all');
    $activeEvidence = (string) ($filters['evidence'] ?? 'all');
    $searchQuery = (string) ($filters['q'] ?? '');
    $onlyMap = !empty($filters['map']);
    $activeSort = (string) ($filters['sort'] ?? 'featured');

    $groupMeta = [
        'all' => ['label' => '📚 ' . __('messages.group_all'), 'border' => 'border-t-orange-600'],
        'historia' => ['label' => '🏛️ ' . __('filament.group_historia'), 'border' => 'border-t-amber-600'],
        'monumentos' => ['label' => '🗼 ' . __('filament.group_monumentos'), 'border' => 'border-t-blue-700'],
        'playas' => ['label' => '🏖️ ' . __('filament.group_playas'), 'border' => 'border-t-sky-600'],
        'fauna' => ['label' => '🦎 ' . __('filament.group_fauna'), 'border' => 'border-t-emerald-700'],
        'flora' => ['label' => '🌲 ' . __('filament.group_flora'), 'border' => 'border-t-green-700'],
        'gastronomia' => ['label' => '🍷 ' . __('filament.group_gastronomia'), 'border' => 'border-t-purple-700'],
        'personajes' => ['label' => '👤 ' . __('filament.group_personajes'), 'border' => 'border-t-rose-700'],
        'fiestas' => ['label' => '🎭 ' . __('filament.group_fiestas'), 'border' => 'border-t-red-700'],
        'refranes' => ['label' => '🗣️ ' . __('filament.group_refranes'), 'border' => 'border-t-indigo-700'],
    ];
@endphp

{{-- ============================================================
     HERO NOCTURNO ATLÁNTICO + BUSCADOR MULTICRITERIO
     ============================================================ --}}
<section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-blue-950 to-violet-950 text-white border-b-4 border-orange-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
        <div class="text-center max-w-4xl mx-auto mb-8">
            <span class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900/90 px-4 py-1.5 text-xs font-bold text-amber-300 mb-5">
                <span aria-hidden="true">🦎</span>
                <span>{{ __('messages.home_hero_badge_chipiona') }}</span>
            </span>
            <h1 class="font-serif text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight mb-4">
                {{ __('messages.home_hero_title_prefix') }} <span class="text-amber-300">Chipiona</span> {{ __('messages.home_hero_title_suffix') }}
            </h1>
            <p class="text-base sm:text-lg text-slate-200 max-w-2xl mx-auto leading-relaxed">
                {{ __('messages.home_hero_subtitle_chipiona') }}
            </p>
        </div>

        {{-- Tarjeta Blanca del Buscador Cómodo y Filtrable --}}
        <form
            action="{{ route('home') }}"
            method="GET"
            role="search"
            aria-label="{{ __('messages.home_search_form_aria') }}"
            class="max-w-4xl mx-auto rounded-2xl border-2 border-amber-500/70 bg-white p-5 sm:p-7 text-slate-950 shadow-2xl space-y-5"
        >
            <div>
                <label for="search-q" class="block text-sm font-extrabold text-slate-950 mb-2">
                    {{ __('messages.home_search_input_label') }}
                </label>
                <div class="flex flex-col sm:flex-row gap-3">
                    <input
                        type="search"
                        id="search-q"
                        name="q"
                        value="{{ $searchQuery }}"
                        placeholder="{{ __('messages.home_search_placeholder_chipiona') }}"
                        class="flex-1 rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-base font-medium text-slate-950 placeholder:text-slate-600 focus:border-orange-700 focus:ring-2 focus:ring-orange-700"
                    >
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-700 px-7 py-3 text-base font-extrabold text-white shadow-md hover:bg-orange-800 transition cursor-pointer"
                    >
                        <span>{{ __('messages.home_search_button_label') }}</span>
                    </button>
                </div>
            </div>

            {{-- 1. Filtrar por tema o apartado --}}
            <div>
                <span class="block text-xs font-extrabold uppercase tracking-wider text-slate-800 mb-2.5">
                    {{ __('messages.home_filter_group_title') }}
                </span>
                <input type="hidden" name="group" id="filter-group-input" value="{{ $activeGroup }}">
                <div class="flex flex-wrap gap-2">
                    @foreach($groupMeta as $key => $meta)
                        @php
                            $isSelected = ($activeGroup === $key) || ($activeGroup === '' && $key === 'all');
                            $count = $groupCounts[$key] ?? 0;
                        @endphp
                        <button
                            type="submit"
                            name="group"
                            value="{{ $key }}"
                            aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                            class="inline-flex items-center gap-1.5 rounded-xl border px-3.5 py-2 text-xs sm:text-sm font-bold transition cursor-pointer {{ $isSelected ? 'border-slate-950 bg-slate-950 text-white shadow-xs' : 'border-slate-300 bg-slate-50 text-slate-900 hover:bg-slate-200' }}"
                        >
                            <span>{{ $meta['label'] }}</span>
                            @if($count > 0)
                                <span class="rounded-full px-1.5 py-0.5 text-xs {{ $isSelected ? 'bg-amber-400 text-slate-950 font-extrabold' : 'bg-slate-200 text-slate-800' }}">{{ $count }}</span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- 2. Filtrar por época/fechas y 3. Filtrar por rigor documental --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-2 border-t border-slate-200">
                <div>
                    <label for="filter-era" class="block text-xs font-extrabold uppercase tracking-wider text-slate-800 mb-1.5">
                        {{ __('messages.home_filter_era_title') }}
                    </label>
                    @php
                        $eraOptions = ['all' => '⏳ ' . __('messages.home_filter_era_all')];
                        foreach(\App\Enums\HistoricalEra::cases() as $eraCase) {
                            $eraOptions[$eraCase->value] = $eraCase->label();
                        }
                    @endphp
                    <x-searchable-select
                        name="era"
                        id="filter-era"
                        :options="$eraOptions"
                        :value="$activeEra ?: 'all'"
                    />
                </div>

                <div>
                    <label for="filter-evidence" class="block text-xs font-extrabold uppercase tracking-wider text-slate-800 mb-1.5">
                        {{ __('messages.home_filter_evidence_title') }}
                    </label>
                    @php
                        $evidenceOptions = ['all' => '⚖️ ' . __('messages.home_filter_evidence_all')];
                        foreach(\App\Enums\EvidenceLevel::cases() as $evCase) {
                            $evidenceOptions[$evCase->value] = $evCase->label();
                        }
                    @endphp
                    <x-searchable-select
                        name="evidence"
                        id="filter-evidence"
                        :options="$evidenceOptions"
                        :value="$activeEvidence ?: 'all'"
                    />
                </div>

                <div>
                    <label for="filter-year-from" class="block text-xs font-extrabold uppercase tracking-wider text-slate-800 mb-1.5">
                        {{ __('messages.home_filter_year_label') }}
                    </label>
                    <div class="flex items-center gap-1.5">
                        <input
                            type="number"
                            id="filter-year-from"
                            name="year_from"
                            min="-10000"
                            max="2100"
                            value="{{ $filters['year_from'] ?? '' }}"
                            placeholder="{{ __('messages.home_filter_year_from_placeholder') }}"
                            class="w-1/2 rounded-xl border border-slate-300 px-2.5 py-2 text-base sm:text-sm font-bold text-slate-950"
                        >
                        <input
                            type="number"
                            name="year_to"
                            min="-10000"
                            max="2100"
                            aria-label="{{ __('messages.home_filter_year_to_aria') }}"
                            value="{{ $filters['year_to'] ?? '' }}"
                            placeholder="{{ __('messages.home_filter_year_to_placeholder') }}"
                            class="w-1/2 rounded-xl border border-slate-300 px-2.5 py-2 text-base sm:text-sm font-bold text-slate-950"
                        >
                    </div>
                </div>

                <div>
                    <label for="filter-sort" class="block text-xs font-extrabold uppercase tracking-wider text-slate-800 mb-1.5">
                        {{ __('messages.home_filter_sort_label') }}
                    </label>
                    <select
                        id="filter-sort"
                        name="sort"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-base sm:text-sm font-bold text-slate-950 focus:border-orange-700 focus:ring-orange-700"
                    >
                        <option value="featured" @selected($activeSort === 'featured')>{{ __('messages.home_sort_featured') }}</option>
                        <option value="chronological_asc" @selected($activeSort === 'chronological_asc')>{{ __('messages.home_sort_chronological_asc') }}</option>
                        <option value="chronological_desc" @selected($activeSort === 'chronological_desc')>{{ __('messages.home_sort_chronological_desc') }}</option>
                        <option value="recent" @selected($activeSort === 'recent')>{{ __('messages.home_sort_recent') }}</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                @if($searchQuery !== '' || ($activeGroup !== '' && $activeGroup !== 'all') || $activeEra !== 'all' || $activeEvidence !== 'all' || !empty($filters['year_from']) || !empty($filters['year_to']))
                    <a
                        href="{{ route('home') }}"
                        class="text-xs font-bold text-slate-700 hover:text-orange-700 underline"
                    >
                        {{ __('messages.clear_all_filters') }}
                    </a>
                @endif
                <button
                    type="submit"
                    class="rounded-xl bg-slate-900 px-5 py-2 text-xs sm:text-sm font-extrabold text-white shadow-xs hover:bg-orange-800 transition cursor-pointer"
                >
                    {{ __('messages.apply_filters') }}
                </button>
            </div>
        </form>
    </div>
</section>

{{-- ============================================================
     BANDA DE ADVERTENCIA METODOLÓGICA: HECHOS VS. MITOS Y LEYENDAS
     ============================================================ --}}
<section aria-label="{{ __('messages.home_truth_commitment_heading') }}" class="border-b border-amber-200 bg-amber-50/90 py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <span class="text-xl shrink-0" aria-hidden="true">🧭</span>
            <p class="text-xs sm:text-sm text-slate-900 leading-relaxed">
                <strong class="font-extrabold text-slate-950">{{ __('messages.home_truth_commitment_title') }}</strong>
                {!! __('messages.home_truth_commitment_body') !!}
            </p>
        </div>
        <div class="flex flex-wrap gap-2 shrink-0">
            <a href="{{ route('home', ['evidence' => 'consolidado']) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-sky-800 bg-sky-50 px-3 py-1 text-xs font-extrabold text-sky-950 hover:bg-sky-100">
                <span>🛡️ {{ __('messages.home_verified_sources_pill') }}</span>
            </a>
            <a href="{{ route('home', ['evidence' => 'tradicion']) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-amber-800 bg-amber-100/80 px-3 py-1 text-xs font-extrabold text-amber-950 hover:bg-amber-200">
                <span>🗣️ {{ __('messages.home_tradition_pill') }}</span>
            </a>
            <a href="{{ route('home', ['evidence' => 'discutido']) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-rose-800 bg-rose-50 px-3 py-1 text-xs font-extrabold text-rose-950 hover:bg-rose-100">
                <span>⚠️ {{ __('messages.home_disputed_pill') }}</span>
            </a>
        </div>
    </div>
</section>

{{-- ============================================================
     EXPLORADOR DE TARJETAS CON PÁGINAS ENLAZADAS Y COORDENADAS
     ============================================================ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
        <div>
            <h2 class="font-serif text-2xl sm:text-3xl font-extrabold text-slate-950">
                {{ __('messages.home_explorer_title') }}
            </h2>
            <p class="text-sm text-slate-800 mt-1">
                {!! __('messages.home_explorer_showing_count', ['count' => '<strong class="text-orange-800 font-extrabold">' . $pages->total() . '</strong>']) !!}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a
                href="{{ route('home', array_merge($filters, ['map' => $onlyMap ? null : 1])) }}"
                class="inline-flex items-center gap-2 rounded-xl border px-4 py-2 text-xs sm:text-sm font-bold transition {{ $onlyMap ? 'border-slate-950 bg-slate-950 text-white' : 'border-slate-300 bg-white text-slate-900 hover:bg-slate-100' }}"
            >
                <span aria-hidden="true">📍</span>
                <span>{{ $onlyMap ? __('messages.home_map_filter_active') : __('messages.home_map_filter_cta') }}</span>
            </a>
            @if(!empty(array_filter($filters)))
                <a
                    href="{{ route('home') }}"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-rose-300 bg-rose-50 px-3.5 py-2 text-xs sm:text-sm font-bold text-rose-950 hover:bg-rose-100"
                >
                    <span>{{ __('messages.clear_filters') }}</span>
                </a>
            @endif
        </div>
    </div>

    @if($pages->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
            @foreach($pages as $pageItem)
                @php
                    $groupValue = $pageItem->content_group?->value ?? 'historia';
                    $borderClass = $groupMeta[$groupValue]['border'] ?? 'border-t-orange-600';
                    $evidence = $pageItem->evidence_level ?? \App\Enums\EvidenceLevel::Consolidated;
                    $badgeClasses = match ($evidence) {
                        \App\Enums\EvidenceLevel::Consolidated, \App\Enums\EvidenceLevel::Documented => 'border-sky-800 bg-sky-50 text-sky-950',
                        \App\Enums\EvidenceLevel::Tradition => 'border-amber-800 bg-amber-50 text-amber-950',
                        \App\Enums\EvidenceLevel::Disputed => 'border-rose-800 bg-rose-50 text-rose-950',
                    };
                    $sourcesCount = is_array($pageItem->source_references) ? count($pageItem->source_references) : 0;
                @endphp
                <article class="flex flex-col justify-between rounded-2xl border border-slate-200 border-t-4 {{ $borderClass }} bg-white shadow-sm hover:shadow-md transition overflow-hidden">
                    <div>
                        {{-- Miniatura visual limpia de la tarjeta (sin texto superpuesto que tape la imagen) --}}
                        <a href="{{ route('pages.show', $pageItem->slug) }}" class="block relative overflow-hidden bg-slate-950 group">
                            @if($pageItem->image_url)
                                <img
                                    src="{{ $pageItem->image_url }}"
                                    alt="{{ $pageItem->getTranslation('title', app()->getLocale()) }}"
                                    width="800"
                                    height="450"
                                    loading="lazy"
                                    decoding="async"
                                    class="h-40 w-full object-cover group-hover:scale-105 transition duration-300"
                                >
                            @else
                                <div class="h-40 w-full bg-gradient-to-br from-slate-950 via-blue-950 to-orange-900"></div>
                            @endif

                            @if($pageItem->content_group)
                                <div class="absolute top-3 left-3">
                                    <span class="rounded-lg bg-slate-950/85 backdrop-blur-xs border border-white/20 px-2.5 py-1 text-xs font-bold text-white shadow-xs">
                                        {{ $pageItem->content_group->label() }}
                                    </span>
                                </div>
                            @endif
                        </a>

                        {{-- Cuerpo de la tarjeta con título legible sobre fondo limpio --}}
                        <div class="p-5 space-y-3">
                            <div class="flex flex-wrap items-center gap-1.5">
                                @if($pageItem->period_label)
                                    <span class="rounded-md bg-amber-100 border border-amber-300 px-2 py-0.5 text-xs font-extrabold text-slate-950">
                                        {{ $pageItem->period_label }}
                                    </span>
                                @endif
                                <span class="inline-flex items-center gap-1 rounded-md border px-2 py-0.5 text-xs font-extrabold {{ $badgeClasses }}">
                                    <span>{{ $evidence->label() }}</span>
                                </span>
                                @if($sourcesCount > 0)
                                    <span class="rounded-md bg-slate-100 border border-slate-300 px-2 py-0.5 text-xs font-bold text-slate-800">
                                        📚 {{ $sourcesCount }} {{ $sourcesCount === 1 ? __('messages.official_source_singular') : __('messages.official_sources_plural') }}
                                    </span>
                                @endif
                            </div>

                            <h3 class="font-serif text-lg font-extrabold text-slate-950 leading-snug">
                                <a href="{{ route('pages.show', $pageItem->slug) }}" class="hover:text-orange-800 transition">
                                    {{ $pageItem->getTranslation('title', app()->getLocale()) }}
                                </a>
                            </h3>

                            <p class="text-sm text-slate-800 leading-relaxed line-clamp-3">
                                {{ $pageItem->getTranslation('excerpt', app()->getLocale()) ?: \Illuminate\Support\Str::limit(strip_tags($pageItem->rendered_content), 160) }}
                            </p>
                        </div>
                    </div>

                    {{-- Pie de tarjeta: Coordenadas GPS, Vídeo, Páginas enlazadas y CTA --}}
                    <div class="px-5 pb-5 pt-3 border-t border-slate-100 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2 text-xs font-bold text-slate-800">
                            @if($pageItem->hasCoordinates())
                                <span class="inline-flex items-center gap-1 text-slate-800">
                                    <span aria-hidden="true">📍</span>
                                    <span>{{ $pageItem->location_name ?: number_format($pageItem->latitude, 4).'° N, '.number_format(abs($pageItem->longitude), 4).'° O' }}</span>
                                </span>
                            @endif
                            @if($pageItem->youtube_url)
                                <span class="inline-flex items-center gap-1 rounded bg-rose-50 px-2 py-0.5 text-rose-950 border border-rose-200">
                                    <span aria-hidden="true">▶️</span>
                                    <span>{{ __('messages.includes_video') }}</span>
                                </span>
                            @endif
                        </div>

                        @if($pageItem->relatedPages->count() > 0)
                            <div>
                                <span class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                                    {{ __('messages.linked_pages_label') }}
                                </span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($pageItem->relatedPages->take(3) as $relPage)
                                        <a
                                            href="{{ route('pages.show', $relPage->slug) }}"
                                            class="inline-flex items-center rounded-md border border-indigo-200 bg-indigo-50 px-2 py-0.5 text-xs font-bold text-indigo-950 hover:bg-indigo-100"
                                        >
                                            {{ \Illuminate\Support\Str::limit($relPage->getTranslation('title', app()->getLocale()), 28) }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <a
                            href="{{ route('pages.show', $pageItem->slug) }}"
                            class="flex items-center justify-center gap-2 w-full rounded-xl bg-slate-950 px-4 py-2.5 text-xs sm:text-sm font-extrabold text-white hover:bg-orange-800 transition"
                        >
                            <span>{{ __('messages.read_full_story') }}</span>
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $pages->links() }}
        </div>
    @else
        <div class="rounded-2xl border-2 border-dashed border-slate-300 bg-white p-12 text-center max-w-2xl mx-auto">
            <p class="text-lg font-extrabold text-slate-950 mb-2">{{ __('messages.no_results') }}</p>
            <p class="text-sm text-slate-700 mb-6">
                {{ __('messages.home_empty_state_desc') }}
            </p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('home') }}" class="rounded-xl bg-slate-950 px-5 py-2.5 text-sm font-bold text-white hover:bg-slate-800">
                    {{ __('messages.view_all_catalog') }}
                </a>
                <a href="{{ route('suggestions.create') }}" class="rounded-xl bg-orange-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-orange-800">
                    {{ __('messages.propose_content_btn') }}
                </a>
            </div>
        </div>
    @endif
</section>

{{-- ============================================================
     REFRÁN DESTACADO Y ÚLTIMAS NOTICIAS DE CHIPIONA
     ============================================================ --}}
<section class="bg-white border-t border-slate-200 py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Refrán popular destacado --}}
        <div class="rounded-2xl border-2 border-amber-500/70 bg-amber-50/60 p-6 flex flex-col justify-between">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-amber-200/80 px-3 py-1 text-xs font-extrabold text-amber-950 mb-4">
                    🗣️ {{ __('messages.home_proverbs_badge') }}
                </span>
                @if($featuredProverb)
                    <blockquote class="font-serif text-xl font-extrabold text-slate-950 leading-snug mb-3">
                        «{{ $featuredProverb->text }}»
                    </blockquote>
                    @if($featuredProverb->meaning)
                        <p class="text-sm text-slate-800 leading-relaxed mb-4">
                            {{ $featuredProverb->meaning }}
                        </p>
                    @endif
                @else
                    <blockquote class="font-serif text-xl font-extrabold text-slate-950 leading-snug mb-3">
                        «{{ __('messages.home_proverb_fallback_text') }}»
                    </blockquote>
                    <p class="text-sm text-slate-800 leading-relaxed mb-4">
                        {{ __('messages.home_proverb_fallback_meaning') }}
                    </p>
                @endif
            </div>
            <div class="flex flex-wrap gap-2 pt-4 border-t border-amber-200">
                <a href="{{ route('proverbs.index') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-950 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800">
                    <span>{{ __('messages.view_all_proverbs') }}</span>
                    <span aria-hidden="true">→</span>
                </a>
                <a href="{{ route('proverbs.index') }}#subir-refran" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-900 bg-white px-4 py-2 text-xs font-bold text-slate-950 hover:bg-amber-100">
                    <span>{{ __('messages.submit_proverb_cta') }}</span>
                </a>
            </div>
        </div>

        {{-- Últimas noticias y hallazgos documentales --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-serif text-2xl font-extrabold text-slate-950">{{ __('messages.home_news_title') }}</h2>
                    <p class="text-sm text-slate-700">{{ __('messages.home_news_subtitle') }}</p>
                </div>
                <a href="{{ route('news.index') }}" class="text-sm font-extrabold text-orange-800 hover:underline">
                    {{ __('messages.view_all_news') }} →
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($latestNews as $newsItem)
                    <article class="rounded-2xl border border-slate-200 bg-stone-50 p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-700 mb-2">
                                <span>{{ format_date($newsItem->published_at ?? $newsItem->created_at) }}</span>
                                @if($newsItem->location_name)
                                    <span>· 📍 {{ $newsItem->location_name }}</span>
                                @endif
                            </div>
                            <h3 class="font-serif text-lg font-extrabold text-slate-950 mb-2">
                                <a href="{{ route('news.show', $newsItem->slug) }}" class="hover:text-orange-800">
                                    {{ $newsItem->getTranslation('title', app()->getLocale()) }}
                                </a>
                            </h3>
                            <p class="text-sm text-slate-800 line-clamp-2">
                                {{ $newsItem->getTranslation('excerpt', app()->getLocale()) ?: \Illuminate\Support\Str::limit(strip_tags($newsItem->rendered_content), 120) }}
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-200 flex items-center justify-between">
                            <a href="{{ route('news.show', $newsItem->slug) }}" class="text-xs font-extrabold text-blue-900 hover:underline">
                                {{ __('messages.read_full_news') }} →
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="md:col-span-2 rounded-2xl border border-slate-200 bg-stone-50 p-6 text-sm text-slate-800">
                        {{ __('messages.home_news_empty') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

{{-- Secciones dinámicas editables en Filament si existen --}}
@if($sections->count())
<section class="bg-stone-50 py-12 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @foreach($sections as $sectionItem)
            <x-section-block :block="$sectionItem" />
        @endforeach
    </div>
</section>
@endif

@endsection
