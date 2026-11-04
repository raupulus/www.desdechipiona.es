@extends('layouts.app')

@section('content')
@php
    $categories = [
        'all' => '🌊 Todos los refranes',
        'mar' => '⚓ La Mar, Mareas y Corrales',
        'campo' => '🍇 El Campo, Huerta y Moscatel',
        'vientos' => '🌬️ Vientos (Levante y Poniente)',
        'fiestas' => '🎭 Fiestas y Tradiciones',
        'vida' => '🏡 Vida Cotidiana del Pueblo',
    ];
@endphp

<header class="bg-gradient-to-br from-slate-950 via-blue-950 to-violet-950 text-white border-b-4 border-orange-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <span class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3.5 py-1 text-xs font-extrabold text-amber-300 mb-4">
            🗣️ Memoria Oral de Nuestros Mayores
        </span>
        <h1 class="font-serif text-3xl sm:text-5xl font-extrabold text-white mb-4">
            Refranero Popular y Dichos de Chipiona
        </h1>
        <p class="max-w-3xl text-base sm:text-lg text-slate-200 leading-relaxed">
            La sabiduría de los antiguos corraleros, marineros y viñistas de Chipiona conservada palabra por palabra. Si en tu casa se dice un refrán o dicho del pueblo, súbelo aquí mismo.
        </p>

        <form action="{{ route('proverbs.index') }}" method="GET" role="search" aria-label="Buscar refranes de Chipiona" class="mt-6 max-w-2xl flex flex-col sm:flex-row gap-3">
            @if($category !== '' && $category !== 'all')
                <input type="hidden" name="category" value="{{ $category }}">
            @endif
            <label for="proverbs-search-q" class="sr-only">Buscar refrán o dicho popular</label>
            <input
                type="search"
                id="proverbs-search-q"
                name="q"
                value="{{ $search }}"
                placeholder="Buscar refrán por palabra, viento, mar o moscatel..."
                class="flex-1 rounded-xl border-2 border-slate-300 bg-white px-4 py-2.5 text-base sm:text-sm font-bold text-slate-950 placeholder:text-slate-600"
            >
            <button type="submit" class="rounded-xl bg-orange-700 px-6 py-2.5 text-sm font-extrabold text-white hover:bg-orange-800 cursor-pointer">
                Buscar
            </button>
            @if($search !== '' || ($category !== '' && $category !== 'all'))
                <a href="{{ route('proverbs.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-500 bg-slate-900 px-4 py-2.5 text-xs font-extrabold text-amber-300 hover:bg-slate-800">
                    Limpiar
                </a>
            @endif
        </form>

        <div class="mt-5 flex flex-wrap gap-2">
            @foreach($categories as $catKey => $catLabel)
                <a
                    href="{{ route('proverbs.index', ['category' => $catKey === 'all' ? null : $catKey, 'q' => $search ?: null]) }}"
                    class="rounded-xl px-4 py-2 text-xs sm:text-sm font-extrabold transition {{ ($category === $catKey || ($category === '' && $catKey === 'all')) ? 'bg-amber-400 text-slate-950' : 'bg-slate-900/90 text-white border border-slate-700 hover:bg-slate-800' }}"
                >
                    {{ $catLabel }}
                </a>
            @endforeach
        </div>
    </div>
</header>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-14">
    {{-- Listado de refranes --}}
    <section>
        @if($proverbs->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($proverbs as $proverb)
                    <article class="flex flex-col justify-between rounded-2xl border border-slate-200 border-t-4 border-t-amber-600 bg-white p-6 shadow-xs">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-2 text-xs font-extrabold">
                                <span class="rounded bg-amber-100 px-2.5 py-0.5 text-amber-950 uppercase">
                                    {{ $categories[$proverb->category] ?? $proverb->category }}
                                </span>
                                @if($proverb->source_code)
                                    <span class="font-mono text-slate-700">{{ $proverb->source_code }}</span>
                                @endif
                            </div>

                            <blockquote class="font-serif text-xl font-extrabold text-slate-950 leading-snug">
                                «{{ $proverb->text }}»
                            </blockquote>

                            @if($proverb->meaning)
                                <p class="text-sm text-slate-800 leading-relaxed">
                                    {{ $proverb->meaning }}
                                </p>
                            @endif
                        </div>

                        <div class="mt-5 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs font-bold text-slate-700">
                            <span>Aportado por: <strong class="text-slate-950">{{ $proverb->author_name }}</strong></span>
                            @if($proverb->relatedPage)
                                <a href="{{ route('pages.show', $proverb->relatedPage->slug) }}" class="text-blue-900 underline font-extrabold">
                                    🔗 {{ \Illuminate\Support\Str::limit($proverb->relatedPage->getTranslation('title', app()->getLocale()), 24) }}
                                </a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $proverbs->links() }}
            </div>
        @else
            <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center">
                <p class="text-base font-extrabold text-slate-950">Aún no hay refranes en esta categoría. ¡Sé el primero en compartir uno debajo!</p>
            </div>
        @endif
    </section>

    {{-- Formulario para subir un refrán --}}
    <section id="subir-refran" class="max-w-3xl mx-auto rounded-2xl border-2 border-slate-900 bg-white p-6 sm:p-10 shadow-lg space-y-6">
        <div>
            <span class="rounded-lg bg-orange-100 px-3 py-1 text-xs font-extrabold text-orange-950">Participación abierta</span>
            <h2 class="font-serif text-2xl sm:text-3xl font-extrabold text-slate-950 mt-2">
                🗣️ Sube tu refrán o dicho de Chipiona
            </h2>
            <p class="text-sm text-slate-800 mt-1">
                ¿Qué decían tus abuelos cuando saltaba el Levante, bajaba el aguaje en los corrales o llegaba la vendimia del moscatel?
            </p>
        </div>

        <x-social-identity-banner />

        <form action="{{ route('proverbs.store') }}" method="POST" class="space-y-5">
            @csrf

            @if(!session('public_social_identity'))
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="prov-author-name" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Tu nombre o familia</label>
                        <input type="text" id="prov-author-name" name="author_name" value="{{ old('author_name') }}" autocomplete="name" placeholder="Ej. Familia Lorenzo / Carmen Naval" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-base sm:text-sm font-medium">
                        <x-input-error :messages="$errors->get('author_name')" class="mt-1" />
                    </div>
                    <div>
                        <label for="prov-author-email" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Tu correo (opcional)</label>
                        <input type="email" id="prov-author-email" name="author_email" value="{{ old('author_email') }}" autocomplete="email" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-base sm:text-sm font-medium">
                        <x-input-error :messages="$errors->get('author_email')" class="mt-1" />
                    </div>
                </div>
            @endif

            <div>
                <label for="prov-text" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Refrán o dicho popular *</label>
                <textarea id="prov-text" name="text" rows="2" required minlength="5" placeholder="Escribe el refrán tal como se dice en Chipiona..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-base font-bold text-slate-950">{{ old('text') }}</textarea>
                <x-input-error :messages="$errors->get('text')" class="mt-1" />
            </div>

            <div>
                <label for="prov-meaning" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">¿Qué significa o cuándo se usa?</label>
                <textarea id="prov-meaning" name="meaning" rows="3" placeholder="Explica brevemente su sentido marinero, del campo o del pueblo..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-base sm:text-sm font-medium">{{ old('meaning') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="prov-category" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Tema del refrán *</label>
                    <x-searchable-select
                        name="category"
                        id="prov-category"
                        :options="[
                            'mar' => 'La Mar, Mareas y Corrales',
                            'campo' => 'El Campo, Huerta y Moscatel',
                            'vientos' => 'Vientos (Levante y Poniente)',
                            'fiestas' => 'Fiestas y Tradiciones',
                            'vida' => 'Vida Cotidiana del Pueblo',
                        ]"
                        value="mar"
                        required
                    />
                </div>

                <div>
                    <label for="prov-page" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Página relacionada (opcional)</label>
                    @php
                        $proverbPageOptions = ['' => '— Ninguna página en concreto —'];
                        foreach($pages as $p) {
                            $proverbPageOptions[(string) $p->id] = $p->getTranslation('title', app()->getLocale());
                        }
                    @endphp
                    <x-searchable-select
                        name="related_page_id"
                        id="prov-page"
                        :options="$proverbPageOptions"
                        value=""
                        placeholder="Escribe para buscar página..."
                    />
                </div>
            </div>

            <x-turnstile-widget action="proverb_submit" />

            <div class="flex items-start gap-2 pt-1">
                <input type="checkbox" id="prov-privacy" name="privacy" value="1" required class="mt-1 rounded border-slate-300 text-orange-700 focus:ring-orange-600">
                <label for="prov-privacy" class="text-xs text-slate-700">
                    {{ __('messages.contact_privacy_accept') }} <a href="{{ route('privacy') }}" target="_blank" rel="noopener noreferrer" class="underline font-bold text-slate-900 hover:text-orange-700">{{ __('messages.contact_privacy_link') }}</a>.
                </label>
            </div>
            @error('privacy')<p class="text-xs text-red-600 font-bold">{{ $message }}</p>@enderror

            <button type="submit" class="w-full rounded-xl bg-orange-700 px-6 py-3.5 text-base font-extrabold text-white shadow-md hover:bg-orange-800 transition cursor-pointer">
                Enviar refrán al archivo de Chipiona
            </button>
        </form>
    </section>
</div>
@endsection
