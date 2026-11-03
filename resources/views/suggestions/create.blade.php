@extends('layouts.app')

@section('content')
<header class="bg-gradient-to-br from-slate-950 via-blue-950 to-violet-950 text-white border-b-4 border-orange-700">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <span class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3.5 py-1 text-xs font-extrabold text-amber-300 mb-4">
            ✍️ Archivo Colaborativo de Chipiona
        </span>
        <h1 class="font-serif text-3xl sm:text-5xl font-extrabold text-white mb-4">
            Sugerir y Aportar Contenido sobre Chipiona
        </h1>
        <p class="text-base sm:text-lg text-slate-200 leading-relaxed">
            Escribe todo el texto que necesites para compartir una historia documentada, un monumento, una receta tradicional, una costumbre o ampliar cualquiera de las páginas publicadas en la web.
        </p>
    </div>
</header>

<section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="rounded-2xl border-2 border-slate-200 bg-white p-6 sm:p-10 shadow-sm space-y-6">
        <x-social-identity-banner />

        <form action="{{ route('suggestions.store') }}" method="POST" class="space-y-6">
            @csrf

            @if(!session('public_social_identity'))
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="sugg-author-name" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Tu nombre</label>
                        <input type="text" id="sugg-author-name" name="author_name" value="{{ old('author_name') }}" placeholder="Ej. Antonio Mellado" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium">
                    </div>
                    <div>
                        <label for="sugg-author-email" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Tu correo electrónico (opcional)</label>
                        <input type="email" id="sugg-author-email" name="author_email" value="{{ old('author_email') }}" placeholder="Para consultarte dudas o avisarte al publicar" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium">
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="sugg-group" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">1. Selecciona el tipo de apartado *</label>
                    <select id="sugg-group" name="content_group" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-bold text-slate-950">
                        @foreach($groups as $group)
                            <option value="{{ $group->value }}" @selected(old('content_group') === $group->value)>
                                {{ $group->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="sugg-related" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">2. ¿Se relaciona con alguna página ya publicada?</label>
                    <select id="sugg-related" name="related_page_id" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-bold text-slate-950">
                        <option value="">— Es un tema nuevo independiente —</option>
                        @foreach($pages as $p)
                            <option value="{{ $p->id }}" @selected((int) old('related_page_id') === $p->id)>
                                {{ $p->getTranslation('title', app()->getLocale()) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="sugg-title" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">3. Título del tema, receta, personaje o historia *</label>
                <input type="text" id="sugg-title" name="title" value="{{ old('title') }}" required minlength="4" maxlength="255" placeholder="Ej. El antiguo cine de verano junto a la playa de Regla..." class="w-full rounded-xl border border-slate-300 px-3.5 py-3 text-base font-bold text-slate-950">
                <x-input-error :messages="$errors->get('title')" class="mt-1" />
            </div>

            <div>
                <label for="sugg-body" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">4. Escribe aquí toda la información, historia o receta con detalle *</label>
                <textarea id="sugg-body" name="body" rows="9" required minlength="20" placeholder="Cuéntanos todos los detalles: fechas, nombres, cómo se preparaba, qué ocurría o qué recuerdas..." class="w-full rounded-xl border border-slate-300 px-4 py-3 text-base font-medium text-slate-950 leading-relaxed">{{ old('body') }}</textarea>
                <x-input-error :messages="$errors->get('body')" class="mt-1" />
            </div>

            <div>
                <label for="sugg-sources" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">5. ¿Es un dato de archivo/prensa o un recuerdo/tradición oral?</label>
                <textarea id="sugg-sources" name="sources_info" rows="2" placeholder="Indica si procede de un documento, libro, recorte de prensa o tradición familiar para clasificar su nivel de evidencia..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-medium">{{ old('sources_info') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="sugg-location" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Lugar en Chipiona (opcional)</label>
                    <input type="text" id="sugg-location" name="location_name" value="{{ old('location_name') }}" placeholder="Ej. Pago de Abulagar / Muelle" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm">
                </div>
                <div>
                    <label for="sugg-lat" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Latitud GPS (opcional)</label>
                    <input type="number" step="0.000001" id="sugg-lat" name="latitude" value="{{ old('latitude') }}" placeholder="36.7378" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm">
                </div>
                <div>
                    <label for="sugg-lng" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">Longitud GPS (opcional)</label>
                    <input type="number" step="0.000001" id="sugg-lng" name="longitude" value="{{ old('longitude') }}" placeholder="-6.4425" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm">
                </div>
            </div>

            <x-turnstile-widget action="content_suggestion" />

            <div class="flex items-start gap-2 pt-1">
                <input type="checkbox" id="sugg-privacy" name="privacy" value="1" required class="mt-1 rounded border-slate-300 text-orange-700 focus:ring-orange-600">
                <label for="sugg-privacy" class="text-xs text-slate-700">
                    {{ __('messages.contact_privacy_accept') }} <a href="{{ route('privacy') }}" target="_blank" rel="noopener noreferrer" class="underline font-bold text-slate-900 hover:text-orange-700">{{ __('messages.contact_privacy_link') }}</a>.
                </label>
            </div>
            @error('privacy')<p class="text-xs text-red-600 font-bold">{{ $message }}</p>@enderror

            <button type="submit" class="w-full rounded-xl bg-orange-700 px-6 py-4 text-base font-extrabold text-white shadow-md hover:bg-orange-800 transition cursor-pointer">
                Enviar aportación al equipo de Desde Chipiona
            </button>
        </form>
    </div>
</section>
@endsection
