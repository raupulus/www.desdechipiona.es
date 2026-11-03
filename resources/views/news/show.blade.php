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
            <span class="rounded-lg bg-slate-900 border border-slate-700 px-3 py-1 text-amber-300">
                📅 {{ format_date($news->published_at ?? $news->created_at) }}
            </span>
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
                            <input type="text" id="news-corr-name" name="author_name" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm">
                        </div>
                        <div>
                            <label for="news-corr-email" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Tu correo (opcional)</label>
                            <input type="email" id="news-corr-email" name="author_email" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm">
                        </div>
                    </div>
                @endif
                <div>
                    <label for="news-proposed" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Corrección propuesta *</label>
                    <textarea id="news-proposed" name="proposed_correction" rows="3" required minlength="10" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm"></textarea>
                </div>
                <x-turnstile-widget action="news_correction" />
                <button type="submit" class="rounded-xl bg-orange-700 px-5 py-2.5 text-sm font-extrabold text-white hover:bg-orange-800">
                    Enviar corrección
                </button>
            </form>
        </section>

        {{-- Comentarios --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-6 space-y-5">
            <h2 class="font-serif text-2xl font-extrabold text-slate-950">💬 Comentarios ({{ $news->comments->count() }})</h2>
            @foreach($news->comments as $comment)
                <div class="rounded-xl border border-slate-200 bg-stone-50 p-4">
                    <div class="flex justify-between text-xs font-bold text-slate-700 mb-1">
                        <span class="text-slate-950">{{ $comment->author_name }}</span>
                        <span>{{ format_date($comment->created_at) }}</span>
                    </div>
                    <p class="text-sm text-slate-900">{{ $comment->body }}</p>
                </div>
            @endforeach

            <form action="{{ route('comments.store') }}" method="POST" class="space-y-4 pt-3 border-t border-slate-200">
                @csrf
                <input type="hidden" name="commentable_type" value="news">
                <input type="hidden" name="commentable_id" value="{{ $news->id }}">
                @if(!session('public_social_identity'))
                    <div>
                        <label for="news-comm-name" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Tu nombre</label>
                        <input type="text" id="news-comm-name" name="author_name" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm">
                    </div>
                @endif
                <div>
                    <label for="news-comm-body" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Tu comentario *</label>
                    <textarea id="news-comm-body" name="body" rows="3" required minlength="5" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm"></textarea>
                </div>
                <x-turnstile-widget action="news_comment" />
                <button type="submit" class="rounded-xl bg-slate-950 px-5 py-2.5 text-sm font-extrabold text-white hover:bg-orange-800">
                    Publicar comentario
                </button>
            </form>
        </section>
    </article>

    <aside class="lg:col-span-4 space-y-6">
        @if($news->hasCoordinates())
            <div class="rounded-2xl border border-slate-200 bg-white p-5 space-y-3">
                <h2 class="font-serif text-lg font-extrabold text-slate-950">📍 Lugar de la noticia</h2>
                <p class="text-sm font-bold text-slate-900">{{ $news->location_name }}</p>
                <a href="https://www.google.com/maps/search/?api=1&query={{ $news->latitude }},{{ $news->longitude }}" target="_blank" rel="noopener noreferrer" class="block text-center rounded-xl bg-blue-900 px-4 py-2.5 text-xs font-extrabold text-white hover:bg-blue-950">
                    🗺️ Ver en Google Maps
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
