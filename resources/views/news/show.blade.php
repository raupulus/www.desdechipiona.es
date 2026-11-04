@extends('layouts.app')

@section('content')
@php
    $title = $news->getTranslation('title', app()->getLocale());
    $excerpt = $news->getTranslation('excerpt', app()->getLocale());
@endphp

<header class="bg-gradient-to-br from-slate-950 via-blue-950 to-violet-950 text-white border-b-4 border-orange-700">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16 space-y-4">
        <nav aria-label="Migas de pan" class="text-xs font-bold text-slate-300 flex flex-wrap items-center gap-2">
            <a href="{{ route('home') }}" class="hover:text-white underline">Inicio</a>
            <span>/</span>
            <a href="{{ route('news.index') }}" class="text-amber-300 hover:text-white underline">Noticias</a>
            <span>/</span>
            <span class="text-white">{{ $title }}</span>
        </nav>

        <div class="flex flex-wrap items-center gap-2 text-xs font-extrabold">
            <span class="rounded-lg bg-orange-700 px-3 py-1 text-white">📰 Noticia Documental</span>
            <time datetime="{{ ($news->published_at ?? $news->created_at)?->toIso8601String() }}" class="rounded-lg bg-slate-900 border border-slate-700 px-3 py-1 text-amber-300">
                📅 {{ format_date($news->published_at ?? $news->created_at) }}
            </time>
            @if($news->evidence_level)
                <span class="rounded-lg bg-sky-900/90 border border-sky-400 px-3 py-1 text-white">
                    {{ $news->evidence_level->label() }}
                </span>
            @endif
        </div>

        <h1 class="font-serif text-3xl sm:text-5xl font-extrabold text-white leading-tight">{{ $title }}</h1>

        @if($excerpt)
            <p class="text-base sm:text-lg text-slate-200 max-w-3xl leading-relaxed">{{ $excerpt }}</p>
        @endif
    </div>
</header>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 lg:grid-cols-12 gap-8">
    <article class="lg:col-span-8 space-y-8">
        @if($news->image_url)
            <figure class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-950 shadow-xs">
                <img
                    src="{{ $news->image_url }}"
                    alt="{{ $title }}"
                    width="1200"
                    height="675"
                    fetchpriority="high"
                    decoding="async"
                    class="w-full max-h-96 object-cover"
                >
            </figure>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-10 shadow-xs prose-chipiona max-w-none">
            {!! $news->rendered_content !!}
        </div>

        @if($news->youtube_embed_url)
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-3">
                <h2 class="font-serif text-xl font-extrabold text-slate-950">▶️ Vídeo relacionado</h2>
                <div class="aspect-video w-full overflow-hidden rounded-xl bg-slate-950">
                    <iframe src="{{ $news->youtube_embed_url }}" title="{{ $title }}" class="h-full w-full border-0" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
        @endif

        {{-- Fuentes Oficiales y Documentación Pública --}}
        @if(!empty($news->source_references) && is_array($news->source_references))
            <section aria-label="Fuentes oficiales y documentación consultada" class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-4">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🏛️</span>
                    <h2 class="font-serif text-xl font-extrabold text-slate-950">Fuentes Oficiales y Documentación Pública</h2>
                </div>
                <p class="text-xs text-slate-700">
                    En cumplimiento de nuestro compromiso de rigor, esta noticia cita exclusivamente fuentes procedentes de archivos históricos, organismos gubernamentales e instituciones académicas oficiales.
                </p>
                <ul class="divide-y divide-slate-100 text-sm space-y-3">
                    @foreach($news->source_references as $source)
                        <li class="pt-3 first:pt-0">
                            <div class="font-bold text-slate-950">
                                @if(is_array($source) && !empty($source['url']))
                                    <a href="{{ $source['url'] }}" target="_blank" rel="noopener noreferrer" class="text-blue-900 hover:text-orange-700 hover:underline">
                                        {{ $source['title'] ?? 'Documento oficial' }} ↗
                                    </a>
                                @elseif(is_array($source))
                                    <span>{{ $source['title'] ?? 'Documento oficial' }}</span>
                                @else
                                    <span>{{ $source }}</span>
                                @endif
                            </div>
                            @if(is_array($source))
                                <div class="text-xs text-slate-600 mt-0.5">
                                    <span class="font-semibold text-slate-800">{{ $source['author'] ?? '' }}</span>
                                    @if(!empty($source['year'])) · <span>{{ $source['year'] }}</span> @endif
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        {{-- Proponer corrección sobre la noticia --}}
        <section class="rounded-2xl border-2 border-amber-500/80 bg-amber-50/70 p-6 space-y-4">
            <h2 class="font-serif text-xl font-extrabold text-slate-950">✏️ Proponer corrección o ampliación sobre esta noticia</h2>
            <x-social-identity-banner />
            <form action="{{ route('corrections.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="correctable_type" value="news">
                <input type="hidden" name="correctable_id" value="{{ $news->id }}">
                @if(!session('public_social_identity'))
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="news-corr-name" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Tu nombre</label>
                            <input type="text" id="news-corr-name" name="author_name" value="{{ old('author_name') }}" autocomplete="name" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-base sm:text-sm">
                            <x-input-error :messages="$errors->get('author_name')" class="mt-1" />
                        </div>
                        <div>
                            <label for="news-corr-email" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Tu correo (opcional)</label>
                            <input type="email" id="news-corr-email" name="author_email" value="{{ old('author_email') }}" autocomplete="email" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-base sm:text-sm">
                            <x-input-error :messages="$errors->get('author_email')" class="mt-1" />
                        </div>
                    </div>
                @endif
                <div>
                    <label for="news-proposed" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Corrección propuesta *</label>
                    <textarea id="news-proposed" name="proposed_correction" rows="3" required minlength="10" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-base sm:text-sm">{{ old('proposed_correction') }}</textarea>
                    <x-input-error :messages="$errors->get('proposed_correction')" class="mt-1" />
                </div>
                <x-turnstile-widget action="news_correction" />
                <div class="flex items-start gap-2 pt-1">
                    <input type="checkbox" id="news-corr-privacy" name="privacy" value="1" required class="mt-1 rounded border-slate-300 text-orange-700 focus:ring-orange-600">
                    <label for="news-corr-privacy" class="text-xs text-slate-700">
                        {{ __('messages.contact_privacy_accept') }} <a href="{{ route('privacy') }}" target="_blank" rel="noopener noreferrer" class="underline font-bold text-slate-900 hover:text-orange-700">{{ __('messages.contact_privacy_link') }}</a>.
                    </label>
                </div>
                @error('privacy')<p class="text-xs text-red-600 font-bold">{{ $message }}</p>@enderror
                <button type="submit" class="rounded-xl bg-orange-700 px-5 py-2.5 text-sm font-extrabold text-white hover:bg-orange-800">
                    Enviar corrección
                </button>
            </form>
        </section>

        {{-- Comentarios --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-6 space-y-5">
            <h2 class="font-serif text-2xl font-extrabold text-slate-950">💬 Comentarios ({{ $news->comments->count() }})</h2>
            @foreach($news->comments as $comment)
                <div class="rounded-xl border border-slate-200 bg-stone-50 p-4 space-y-1">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-700 mb-1">
                        <div class="flex items-center gap-2">
                            <span class="text-slate-950">{{ $comment->author_name }}</span>
                            @if($comment->oauth_provider)
                                <span class="rounded bg-blue-100 px-2 py-0.5 text-xs font-bold text-blue-950">
                                    Verificado con {{ ucfirst($comment->oauth_provider) }}
                                </span>
                            @endif
                        </div>
                        <time datetime="{{ $comment->created_at?->toIso8601String() }}">{{ format_date($comment->created_at) }}</time>
                    </div>
                    <p class="text-sm text-slate-900 leading-relaxed">{{ $comment->body }}</p>
                </div>
            @endforeach

            <div class="pt-4 border-t border-slate-200 space-y-4">
                <h3 class="text-base font-extrabold text-slate-950">Dejar un comentario</h3>

                @if(!session('public_social_identity') && !auth()->check())
                    <div class="rounded-xl border-2 border-slate-300 bg-stone-50 p-5 text-center space-y-3">
                        <p class="text-sm font-bold text-slate-900">
                            🔒 Para proteger el portal contra spam y mantener el rigor de la hemeroteca, se requiere identificación previa en un clic:
                        </p>
                        <x-social-identity-banner />
                    </div>
                @else
                    @php
                        $socialUser = session('public_social_identity');
                        $userName = $socialUser['name'] ?? auth()->user()?->name ?? 'Usuario Verificado';
                        $userProvider = $socialUser['provider'] ?? 'cuenta';
                    @endphp
                    <div class="flex items-center justify-between rounded-xl bg-slate-100 p-3 text-xs font-bold text-slate-800">
                        <span>Comentando como: <strong class="text-slate-950">{{ $userName }}</strong> ({{ ucfirst($userProvider) }})</span>
                        <form action="{{ route('social.disconnect') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-red-700 hover:underline cursor-pointer">Cambiar cuenta</button>
                        </form>
                    </div>

                    <form action="{{ route('comments.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="commentable_type" value="news">
                        <input type="hidden" name="commentable_id" value="{{ $news->id }}">
                        <div>
                            <label for="news-comm-body" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Tu comentario *</label>
                            <textarea id="news-comm-body" name="body" rows="3" required minlength="5" placeholder="Escribe tu reflexión o memoria sobre esta noticia..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-base sm:text-sm focus:border-orange-600 focus:ring-orange-600">{{ old('body') }}</textarea>
                            <x-input-error :messages="$errors->get('body')" class="mt-1" />
                            <p class="text-xs text-slate-500 mt-1">Los enlaces y menciones publicitarias externas no están permitidos.</p>
                        </div>
                        <x-turnstile-widget action="news_comment" />
                        <div class="flex items-start gap-2 pt-1">
                            <input type="checkbox" id="news-comm-privacy" name="privacy" value="1" required class="mt-1 rounded border-slate-300 text-orange-700 focus:ring-orange-600">
                            <label for="news-comm-privacy" class="text-xs text-slate-700">
                                {{ __('messages.contact_privacy_accept') }} <a href="{{ route('privacy') }}" target="_blank" rel="noopener noreferrer" class="underline font-bold text-slate-900 hover:text-orange-700">{{ __('messages.contact_privacy_link') }}</a>.
                            </label>
                        </div>
                        @error('privacy')<p class="text-xs text-red-600 font-bold">{{ $message }}</p>@enderror
                        <div class="flex items-center justify-between">
                            <button type="submit" class="rounded-xl bg-slate-950 px-5 py-2.5 text-sm font-extrabold text-white hover:bg-orange-800 transition">
                                Publicar comentario
                            </button>
                            <span class="text-xs text-slate-500">Moderación previa activa</span>
                        </div>
                    </form>
                @endif
            </div>
        </section>
    </article>

    <aside class="lg:col-span-4 space-y-6">
        @if($news->hasCoordinates())
            <div class="rounded-2xl border border-slate-200 bg-white p-5 space-y-3">
                <h2 class="font-serif text-lg font-extrabold text-slate-950">📍 Lugar de la noticia</h2>
                @if($news->location_name)
                    <p class="text-sm font-bold text-slate-900">{{ $news->location_name }}</p>
                @endif
                <div class="aspect-4/3 w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-100 shadow-inner">
                    <iframe
                        src="https://maps.google.com/maps?q={{ $news->latitude }},{{ $news->longitude }}&amp;z=15&amp;output=embed"
                        title="Mapa de {{ $title }}"
                        class="h-full w-full border-0"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>
                <div class="flex items-center justify-between text-xs font-mono font-bold text-slate-700">
                    <span>Lat: {{ number_format($news->latitude, 4) }}</span>
                    <span>Lng: {{ number_format($news->longitude, 4) }}</span>
                </div>
                <a href="https://www.google.com/maps/search/?api=1&amp;query={{ $news->latitude }},{{ $news->longitude }}" target="_blank" rel="noopener noreferrer" class="block text-center rounded-xl bg-blue-900 px-4 py-2.5 text-xs font-extrabold text-white hover:bg-blue-950 transition">
                    🗺️ Abrir ubicación en Google Maps
                </a>
            </div>
        @endif

        @if($news->relatedPages->count() > 0)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 space-y-3">
                <h2 class="font-serif text-lg font-extrabold text-slate-950">🔗 Páginas relacionadas</h2>
                <ul class="space-y-2">
                    @foreach($news->relatedPages as $relPage)
                        <li>
                            <a href="{{ route('pages.show', $relPage->slug) }}" class="block rounded-xl border border-slate-200 bg-stone-50 p-3 text-sm font-bold text-slate-950 hover:border-orange-600">
                                {{ $relPage->getTranslation('title', app()->getLocale()) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </aside>
</div>
@endsection
