@extends('layouts.app')

@section('content')
@php
    $title = $page->getTranslation('title', app()->getLocale());
    $excerpt = $page->getTranslation('excerpt', app()->getLocale());
    $evidenceNote = $page->getTranslation('evidence_note', app()->getLocale());
    $evidence = $page->evidence_level ?? \App\Enums\EvidenceLevel::Consolidated;
    $sources = is_array($page->source_references) ? $page->source_references : [];
    $rawContent = $page->getTranslation('content', app()->getLocale());

    $evidenceBannerClasses = match ($evidence) {
        \App\Enums\EvidenceLevel::Consolidated, \App\Enums\EvidenceLevel::Documented => 'border-sky-800 bg-sky-50 text-sky-950',
        \App\Enums\EvidenceLevel::Tradition => 'border-amber-800 bg-amber-50 text-amber-950',
        \App\Enums\EvidenceLevel::Disputed => 'border-rose-800 bg-rose-50 text-rose-950',
    };
@endphp

{{-- ============================================================
     CABECERA VISUAL 16:9 DEL ARTÍCULO / FICHA PATRIMONIAL
     ============================================================ --}}
<header class="bg-gradient-to-br from-slate-950 via-blue-950 to-violet-950 text-white border-b-4 border-orange-700">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        {{-- Migas de pan --}}
        <nav aria-label="Migas de pan" class="mb-5 text-xs font-bold text-slate-300 flex flex-wrap items-center gap-2">
            <a href="{{ route('home') }}" class="hover:text-white underline">Inicio</a>
            <span aria-hidden="true">/</span>
            @if($page->content_group)
                <a href="{{ route('home', ['group' => $page->content_group->value]) }}" class="text-amber-300 hover:text-white underline">
                    {{ $page->content_group->label() }}
                </a>
                <span aria-hidden="true">/</span>
            @endif
            <span class="text-white">{{ $title }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-7 space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    @if($page->content_group)
                        <span class="rounded-lg bg-orange-700 px-3 py-1 text-xs font-extrabold uppercase tracking-wider text-white">
                            {{ $page->content_group->label() }}
                        </span>
                    @endif
                    @if($page->period_label)
                        <span class="rounded-lg bg-amber-400 px-3 py-1 text-xs font-extrabold text-slate-950">
                            ⏳ {{ $page->period_label }}
                        </span>
                    @endif
                    @if($page->historical_era)
                        <span class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-1 text-xs font-semibold text-slate-200">
                            {{ $page->historical_era->label() }}
                        </span>
                    @endif
                </div>

                <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-tight">
                    {{ $title }}
                </h1>

                @if($excerpt)
                    <p class="text-base sm:text-lg text-slate-200 leading-relaxed">
                        {{ $excerpt }}
                    </p>
                @endif

                <div class="pt-2 flex flex-wrap items-center gap-4 text-xs text-slate-300">
                    <span>✍️ Investigación y redacción: <strong class="text-white">Raúl Caro Pastorino</strong> (<a href="https://raupulus.dev" target="_blank" rel="noopener noreferrer" class="underline text-amber-300">raupulus.dev</a>)</span>
                    @if($page->published_at)
                        <span>· Actualizado: {{ format_date($page->updated_at ?? $page->published_at) }}</span>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-5">
                @if($page->image_url)
                    <figure class="overflow-hidden rounded-2xl border-2 border-amber-400/70 bg-slate-900 shadow-2xl">
                        <img
                            src="{{ $page->image_url }}"
                            alt="{{ $title }}"
                            width="1200"
                            height="675"
                            fetchpriority="high"
                            decoding="async"
                            class="w-full aspect-video object-cover"
                        >
                    </figure>
                @else
                    <div class="rounded-2xl border-2 border-slate-800 bg-slate-900/90 p-6 shadow-xl space-y-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/logo.webp') }}" alt="Desde Chipiona" class="h-12 w-12 rounded-full border border-amber-400">
                            <div>
                                <p class="text-xs font-extrabold uppercase tracking-wider text-amber-300">Ficha Documental</p>
                                <p class="text-sm font-bold text-white">Archivo Desde Chipiona</p>
                            </div>
                        </div>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Cada entrada cuenta con metadatos específicos para redes sociales y trazabilidad hacia las fuentes del corpus documental de Chipiona.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</header>

{{-- ============================================================
     CONTENIDO PRINCIPAL + BARRA LATERAL (MAPA, FUENTES, ENLACES)
     ============================================================ --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        {{-- Columna principal de lectura --}}
        <article class="lg:col-span-8 space-y-8">
            {{-- Advertencia de rigor documental: Hechos contrastados vs. mitos/leyendas --}}
            <div role="note" aria-label="Nivel de verificación histórica" class="rounded-2xl border-2 p-5 {{ $evidenceBannerClasses }}">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                    <span class="text-sm font-extrabold uppercase tracking-wider">
                        ⚖️ {{ $evidence->label() }}
                    </span>
                    @if(count($sources) > 0)
                        <div class="flex flex-wrap gap-1.5">
                            <span class="rounded-lg bg-white/90 px-3 py-1 font-bold text-xs text-slate-950 border border-current/30 shadow-2xs">
                                📚 {{ count($sources) }} {{ count($sources) === 1 ? (app()->getLocale() === 'en' ? 'verified official source' : 'fuente oficial verificada') : (app()->getLocale() === 'en' ? 'verified official sources' : 'fuentes oficiales verificadas') }}
                            </span>
                        </div>
                    @endif
                </div>
                <p class="text-sm leading-relaxed">
                    {{ $evidenceNote ?: 'Esta ficha distingue expresamente los datos verificados en fuentes documentales y arqueológicas de las tradiciones orales o leyendas locales de Chipiona.' }}
                </p>
            </div>

            {{-- Cuerpo del artículo (soporta bloques modulares, HTML o Markdown periodístico) --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-10 shadow-xs">
                @if(is_array($rawContent))
                    <div class="space-y-6">
                        @foreach($rawContent as $block)
                            @if(($block['type'] ?? '') === 'text')
                                <div class="prose-chipiona">{!! \App\Models\Page::sanitizeHtmlString((string) ($block['data']['content'] ?? '')) !!}</div>
                            @elseif(($block['type'] ?? '') === 'cta')
                                <div class="rounded-2xl border border-orange-200 bg-orange-50 p-6">
                                    <h3 class="font-serif text-xl font-extrabold text-slate-950 mb-2">{{ $block['data']['title'] ?? '' }}</h3>
                                    <p class="text-sm text-slate-800 mb-4">{{ $block['data']['description'] ?? '' }}</p>
                                    @if(!empty($block['data']['button_url']))
                                        <a href="{{ \App\Models\Page::sanitizeUrlString((string) $block['data']['button_url']) }}" class="inline-flex items-center rounded-xl bg-orange-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-orange-800">
                                            {{ $block['data']['button_text'] ?? __('messages.read_more') }}
                                        </a>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="prose-chipiona max-w-none">
                        {!! $page->rendered_content !!}
                    </div>
                @endif
            </div>

            {{-- Vídeo opcional de YouTube --}}
            @if($page->youtube_embed_url)
                <section aria-label="Vídeo documental relacionado" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-3">
                    <h2 class="font-serif text-xl font-extrabold text-slate-950">▶️ Vídeo documental relacionado</h2>
                    <div class="aspect-video w-full overflow-hidden rounded-xl bg-slate-950">
                        <iframe
                            src="{{ $page->youtube_embed_url }}"
                            title="Vídeo sobre {{ $title }}"
                            class="h-full w-full border-0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                            loading="lazy"
                        ></iframe>
                    </div>
                </section>
            @endif

            {{-- Fuentes Oficiales y Documentación Pública Consultada --}}
            @if(!empty($page->source_references) && is_array($page->source_references))
                <section aria-label="Fuentes oficiales y documentación consultada" class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🏛️</span>
                        <h2 class="font-serif text-xl font-extrabold text-slate-950">Fuentes Oficiales y Documentación Pública</h2>
                    </div>
                    <p class="text-xs text-slate-700">
                        En cumplimiento de nuestro compromiso de rigor, este artículo cita exclusivamente fuentes procedentes de archivos históricos, organismos gubernamentales e instituciones académicas oficiales.
                    </p>
                    <ul class="divide-y divide-slate-100 text-sm space-y-3">
                        @foreach($page->source_references as $source)
                            <li class="pt-3 first:pt-0">
                                <div class="font-bold text-slate-950">
                                    @if(!empty($source['url']))
                                        <a href="{{ $source['url'] }}" target="_blank" rel="noopener noreferrer" class="text-blue-900 hover:text-orange-700 hover:underline">
                                            {{ $source['title'] ?? 'Documento oficial' }} ↗
                                        </a>
                                    @else
                                        <span>{{ $source['title'] ?? 'Documento oficial' }}</span>
                                    @endif
                                </div>
                                <div class="text-xs text-slate-600 mt-0.5">
                                    <span class="font-semibold text-slate-800">{{ $source['author'] ?? '' }}</span>
                                    @if(!empty($source['year'])) · <span>{{ $source['year'] }}</span> @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            {{-- Autoría, Propiedad Intelectual Privada y Cómo Citar (Licencia de Compartición con Atribución) --}}
            <section aria-label="{{ __('messages.authorship_box_title') }}" class="rounded-2xl border border-slate-200 bg-stone-50 p-6 space-y-3">
                <div class="flex items-center gap-2">
                    <span class="text-lg" aria-hidden="true">⚖️</span>
                    <h2 class="font-serif text-lg font-extrabold text-slate-950">{{ __('messages.authorship_box_title') }}</h2>
                </div>
                <p class="text-xs sm:text-sm text-slate-800 leading-relaxed">
                    {{ __('messages.authorship_box_text') }}
                </p>
                <div class="rounded-xl border border-slate-200 bg-white p-3.5 text-xs text-slate-900">
                    <p class="font-extrabold text-slate-950 mb-1">{{ __('messages.authorship_how_to_cite') }}</p>
                    <p class="font-mono text-slate-800 select-all">
                        Caro Pastorino, Raúl ({{ date('Y') }}). «{{ $title }}». En Desde Chipiona ({{ url()->current() }}).
                    </p>
                </div>
            </section>

            {{-- Caja para proponer una corrección sobre esta página --}}
            <section id="proponer-correccion" class="rounded-2xl border-2 border-amber-500/80 bg-amber-50/70 p-6 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="font-serif text-xl font-extrabold text-slate-950">
                            ✏️ ¿Has detectado una errata o tienes un dato documental más preciso?
                        </h2>
                        <p class="text-sm text-slate-800 mt-1">
                            Ayúdanos a mejorar esta página. Tu propuesta llega directamente a la pestaña «Sugerencias del contenido» de nuestra intranet para su revisión.
                        </p>
                    </div>
                    <button
                        type="button"
                        onclick="document.getElementById('correction-dialog').showModal()"
                        class="shrink-0 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-950 px-5 py-3 text-sm font-extrabold text-white hover:bg-orange-800 transition cursor-pointer"
                    >
                        <span>Proponer corrección</span>
                    </button>
                </div>
            </section>

            {{-- Modal nativo <dialog> para enviar solicitud de corrección --}}
            <dialog id="correction-dialog" aria-labelledby="correction-dialog-title" class="m-auto w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl border-2 border-slate-900 bg-white p-0 text-slate-950 shadow-2xl backdrop:bg-slate-950/75">
                <div class="p-6 sm:p-8 space-y-5">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-4">
                        <div>
                            <h3 id="correction-dialog-title" class="font-serif text-2xl font-extrabold text-slate-950">Proponer corrección documental</h3>
                            <p class="text-xs font-bold text-slate-700 mt-1">Página: {{ $title }}</p>
                        </div>
                        <button
                            type="button"
                            onclick="document.getElementById('correction-dialog').close()"
                            class="rounded-lg border border-slate-300 px-3 py-1 text-xs font-extrabold text-slate-900 hover:bg-slate-100"
                        >
                            ✕ Cerrar
                        </button>
                    </div>

                    <x-social-identity-banner />

                    <form action="{{ route('corrections.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="correctable_type" value="page">
                        <input type="hidden" name="correctable_id" value="{{ $page->id }}">

                        @if(!session('public_social_identity'))
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="corr-author-name" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Tu nombre</label>
                                    <input type="text" id="corr-author-name" name="author_name" value="{{ old('author_name') }}" autocomplete="name" placeholder="Ej. María Gómez" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-base sm:text-sm font-medium">
                                    <x-input-error :messages="$errors->get('author_name')" class="mt-1" />
                                </div>
                                <div>
                                    <label for="corr-author-email" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Tu correo (opcional)</label>
                                    <input type="email" id="corr-author-email" name="author_email" value="{{ old('author_email') }}" autocomplete="email" placeholder="Para avisarte al aprobarla" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-base sm:text-sm font-medium">
                                    <x-input-error :messages="$errors->get('author_email')" class="mt-1" />
                                </div>
                            </div>
                        @endif

                        <div>
                            <label for="quoted_text" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Frase o dato actual que deseas corregir (opcional)</label>
                            <input type="text" id="quoted_text" name="quoted_text" value="{{ old('quoted_text') }}" placeholder="Copia aquí la frase o fecha que aparece en el texto..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-base sm:text-sm font-medium">
                            <x-input-error :messages="$errors->get('quoted_text')" class="mt-1" />
                        </div>

                        <div>
                            <label for="proposed_correction" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">¿Cuál es la corrección o aclaración propuesta? *</label>
                            <textarea id="proposed_correction" name="proposed_correction" rows="4" required minlength="10" placeholder="Explica con detalle el dato correcto..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-base sm:text-sm font-medium">{{ old('proposed_correction') }}</textarea>
                            <x-input-error :messages="$errors->get('proposed_correction')" class="mt-1" />
                        </div>

                        <div>
                            <label for="sources_info" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Fuente documental, libro o testimonio (opcional)</label>
                            <input type="text" id="sources_info" name="sources_info" value="{{ old('sources_info') }}" placeholder="Ej. Archivo Municipal, libro de bautismos, fotografía familiar..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-base sm:text-sm font-medium">
                            <x-input-error :messages="$errors->get('sources_info')" class="mt-1" />
                        </div>

                        <x-turnstile-widget action="correction" />

                        <div class="flex items-start gap-2 pt-1">
                            <input type="checkbox" id="page-corr-privacy" name="privacy" value="1" required class="mt-1 rounded border-slate-300 text-orange-700 focus:ring-orange-600">
                            <label for="page-corr-privacy" class="text-xs text-slate-700">
                                {{ __('messages.contact_privacy_accept') }} <a href="{{ route('privacy') }}" target="_blank" rel="noopener noreferrer" class="underline font-bold text-slate-900 hover:text-orange-700">{{ __('messages.contact_privacy_link') }}</a>.
                            </label>
                        </div>
                        @error('privacy')<p class="text-xs text-red-600 font-bold">{{ $message }}</p>@enderror

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" onclick="document.getElementById('correction-dialog').close()" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-800 hover:bg-slate-100">
                                Cancelar
                            </button>
                            <button type="submit" class="rounded-xl bg-orange-700 px-6 py-2.5 text-sm font-extrabold text-white hover:bg-orange-800">
                                Enviar propuesta de corrección
                            </button>
                        </div>
                    </form>
                </div>
            </dialog>

            {{-- Sección de Comentarios y Memoria Vecinal --}}
            <section id="comentarios" aria-label="Comentarios y recuerdos vecinales" class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-6">
                <div>
                    <h2 class="font-serif text-2xl font-extrabold text-slate-950">
                        💬 Comentarios y Recuerdos ({{ $page->comments->count() }})
                    </h2>
                    <p class="text-sm text-slate-700 mt-1">
                        Comparte tus recuerdos o impresiones sobre este lugar o acontecimiento de Chipiona.
                    </p>
                </div>

                @if($page->comments->count() > 0)
                    <div class="space-y-4">
                        @foreach($page->comments as $comment)
                            <div class="rounded-xl border border-slate-200 bg-stone-50 p-4 space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="font-extrabold text-sm text-slate-950">{{ $comment->author_name }}</span>
                                        @if($comment->oauth_provider)
                                            <span class="rounded bg-blue-100 px-2 py-0.5 text-xs font-bold text-blue-950">
                                                Verificado con {{ ucfirst($comment->oauth_provider) }}
                                            </span>
                                        @endif
                                    </div>
                                    <time datetime="{{ $comment->created_at?->toIso8601String() }}" class="text-xs font-semibold text-slate-700">{{ format_date($comment->created_at) }}</time>
                                </div>
                                <p class="text-sm text-slate-900 leading-relaxed">{{ $comment->body }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="pt-4 border-t border-slate-200 space-y-4">
                    <h3 class="text-base font-extrabold text-slate-950">Dejar un comentario</h3>

                    @if(!session('public_social_identity') && !auth()->check())
                        <div class="rounded-xl border-2 border-slate-300 bg-stone-50 p-5 text-center space-y-3">
                            <p class="text-sm font-bold text-slate-900">
                                🔒 Para proteger el portal contra spam y mantener el rigor del archivo, se requiere identificación previa en un clic:
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
                            <input type="hidden" name="commentable_type" value="page">
                            <input type="hidden" name="commentable_id" value="{{ $page->id }}">

                            <div>
                                <label for="comment-body" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Tu comentario o recuerdo *</label>
                                <textarea id="comment-body" name="body" rows="3" required minlength="5" placeholder="Escribe aquí tu comentario con respeto (sin enlaces web)..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-base sm:text-sm font-medium">{{ old('body') }}</textarea>
                                <x-input-error :messages="$errors->get('body')" class="mt-1" />
                            </div>

                            <p class="text-xs text-slate-600">
                                🛡️ Todos los comentarios pasan por moderación previa antes de su publicación para garantizar el respeto y la calidad del archivo.
                            </p>

                            <x-turnstile-widget action="comment" />

                            <div class="flex items-start gap-2 pt-1">
                                <input type="checkbox" id="page-comment-privacy" name="privacy" value="1" required class="mt-1 rounded border-slate-300 text-orange-700 focus:ring-orange-600">
                                <label for="page-comment-privacy" class="text-xs text-slate-700">
                                    {{ __('messages.contact_privacy_accept') }} <a href="{{ route('privacy') }}" target="_blank" rel="noopener noreferrer" class="underline font-bold text-slate-900 hover:text-orange-700">{{ __('messages.contact_privacy_link') }}</a>.
                                </label>
                            </div>
                            @error('privacy')<p class="text-xs text-red-600 font-bold">{{ $message }}</p>@enderror

                            <button type="submit" class="rounded-xl bg-slate-950 px-6 py-3 text-sm font-extrabold text-white hover:bg-orange-800 transition cursor-pointer">
                                Enviar comentario a moderación
                            </button>
                        </form>
                    @endif
                </div>
            </section>
        </article>

        {{-- Barra lateral: Coordenadas GPS / Google Maps, Páginas Enlazadas y Noticias --}}
        <aside class="lg:col-span-4 space-y-6">
            {{-- Ubicación GPS y Mapa en Chipiona --}}
            @if($page->hasCoordinates())
                <div class="rounded-2xl border-2 border-slate-200 bg-white p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="font-serif text-lg font-extrabold text-slate-950">📍 Ubicación en Chipiona</h2>
                        <span class="rounded bg-emerald-100 px-2 py-0.5 font-mono text-xs font-bold text-emerald-950">GPS</span>
                    </div>
                    @if($page->location_name)
                        <p class="text-sm font-bold text-slate-900">{{ $page->location_name }}</p>
                    @endif
                    <div class="aspect-4/3 w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-100 shadow-inner">
                        <iframe
                            src="https://maps.google.com/maps?q={{ $page->latitude }},{{ $page->longitude }}&amp;z=15&amp;output=embed"
                            title="Mapa de {{ $title }}"
                            class="h-full w-full border-0"
                            allowfullscreen
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    </div>
                    <div class="flex items-center justify-between text-xs font-mono font-bold text-slate-700">
                        <span>Lat: {{ number_format($page->latitude, 4) }}</span>
                        <span>Lng: {{ number_format($page->longitude, 4) }}</span>
                    </div>
                    <a
                        href="https://www.google.com/maps/search/?api=1&amp;query={{ $page->latitude }},{{ $page->longitude }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-center gap-2 w-full rounded-xl bg-blue-900 px-4 py-2.5 text-xs font-extrabold text-white hover:bg-blue-950 transition"
                    >
                        <span>🗺️ Abrir ubicación en Google Maps</span>
                    </a>
                </div>
            @endif

            {{-- Páginas Enlazadas (Red de Conocimiento) --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs space-y-4">
                <h2 class="font-serif text-lg font-extrabold text-slate-950">🔗 Páginas Enlazadas</h2>
                <p class="text-xs text-slate-700">
                    Todo el contenido de Desde Chipiona está conectado entre sí para que puedas seguir el hilo histórico.
                </p>
                @if(isset($allLinkedPages) && $allLinkedPages->count() > 0)
                    <ul class="space-y-2.5">
                        @foreach($allLinkedPages as $linked)
                            <li>
                                <a
                                    href="{{ route('pages.show', $linked->slug) }}"
                                    class="block rounded-xl border border-slate-200 bg-stone-50 p-3 hover:border-orange-600 hover:bg-orange-50/40 transition"
                                >
                                    <span class="block text-xs font-extrabold uppercase text-orange-800">
                                        {{ $linked->content_group?->label() ?? 'Ficha relacionada' }}
                                        @if($linked->period_label) · {{ $linked->period_label }} @endif
                                    </span>
                                    <span class="block font-bold text-sm text-slate-950 mt-0.5">
                                        {{ $linked->getTranslation('title', app()->getLocale()) }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-xs font-extrabold text-blue-900 hover:underline">
                        Explorar todo el catálogo relacionado →
                    </a>
                @endif
            </div>

            {{-- Noticias relacionadas --}}
            @if($page->news->count() > 0)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs space-y-3">
                    <h2 class="font-serif text-lg font-extrabold text-slate-950">📰 Noticias relacionadas</h2>
                    <ul class="space-y-2.5">
                        @foreach($page->news as $relatedNews)
                            <li>
                                <a href="{{ route('news.show', $relatedNews->slug) }}" class="block text-sm font-bold text-blue-900 hover:underline">
                                    {{ $relatedNews->getTranslation('title', app()->getLocale()) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </aside>
    </div>
</div>
@endsection
