@extends('layouts.app')

@section('content')
<header class="bg-gradient-to-br from-slate-950 via-blue-950 to-violet-950 text-white border-b-4 border-orange-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <span class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3.5 py-1 text-xs font-extrabold text-amber-300 mb-4">
            📰 Sección independiente de Noticias y Nuevos Legajos
        </span>
        <h1 class="font-serif text-3xl sm:text-5xl font-extrabold text-white mb-4">
            Noticias y Hallazgos Documentales de Chipiona
        </h1>
        <p class="max-w-3xl text-base sm:text-lg text-slate-200 leading-relaxed">
            Crónicas de investigación, nuevos documentos localizados en archivos históricos y actualidad del patrimonio natural y cultural de Chipiona.
        </p>

        <form action="{{ route('news.index') }}" method="GET" class="mt-6 max-w-2xl flex flex-col sm:flex-row gap-3">
            <input
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="Buscar en noticias y hallazgos..."
                class="flex-1 rounded-xl border-2 border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-950 placeholder:text-slate-600"
            >
            <button type="submit" class="rounded-xl bg-orange-700 px-6 py-2.5 text-sm font-extrabold text-white hover:bg-orange-800 cursor-pointer">
                Buscar
            </button>
        </form>
    </div>
</header>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($newsItems->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
            @foreach($newsItems as $news)
                <article class="flex flex-col justify-between rounded-2xl border border-slate-200 border-t-4 border-t-orange-700 bg-white p-6 shadow-xs hover:shadow-md transition">
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2 text-xs font-bold text-slate-700">
                            <span>📅 {{ format_date($news->published_at ?? $news->created_at) }}</span>
                            @if($news->evidence_level)
                                <span class="rounded bg-sky-50 border border-sky-800 px-2 py-0.5 text-sky-950 font-extrabold">
                                    {{ $news->evidence_level->label() }}
                                </span>
                            @endif
                        </div>

                        <h2 class="font-serif text-xl font-extrabold text-slate-950 leading-snug">
                            <a href="{{ route('news.show', $news->slug) }}" class="hover:text-orange-800">
                                {{ $news->getTranslation('title', app()->getLocale()) }}
                            </a>
                        </h2>

                        <p class="text-sm text-slate-800 leading-relaxed line-clamp-3">
                            {{ $news->getTranslation('excerpt', app()->getLocale()) ?: \Illuminate\Support\Str::limit(strip_tags($news->rendered_content), 160) }}
                        </p>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 space-y-3">
                        @if($news->hasCoordinates())
                            <p class="text-xs font-bold text-slate-800">
                                📍 {{ $news->location_name ?: number_format($news->latitude, 4).', '.number_format($news->longitude, 4) }}
                            </p>
                        @endif

                        @if($news->relatedPages->count() > 0)
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($news->relatedPages->take(2) as $relPage)
                                    <a href="{{ route('pages.show', $relPage->slug) }}" class="rounded bg-indigo-50 border border-indigo-200 px-2 py-0.5 text-xs font-bold text-indigo-950">
                                        🔗 {{ \Illuminate\Support\Str::limit($relPage->getTranslation('title', app()->getLocale()), 26) }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <a href="{{ route('news.show', $news->slug) }}" class="inline-flex items-center gap-1.5 text-sm font-extrabold text-orange-800 hover:underline">
                            <span>Leer crónica completa</span>
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $newsItems->links() }}
        </div>
    @else
        <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center">
            <p class="text-base font-extrabold text-slate-950">{{ __('messages.no_results') }}</p>
        </div>
    @endif
</section>
@endsection
