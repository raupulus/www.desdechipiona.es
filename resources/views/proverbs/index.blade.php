@extends('layouts.app')

@section('content')
@php
    $categories = [
        'all' => __('messages.proverbs_cat_all'),
        'mar' => __('messages.proverbs_cat_mar'),
        'campo' => __('messages.proverbs_cat_campo'),
        'vientos' => __('messages.proverbs_cat_vientos'),
        'fiestas' => __('messages.proverbs_cat_fiestas'),
        'vida' => __('messages.proverbs_cat_vida'),
    ];
@endphp

<header class="bg-gradient-to-br from-slate-950 via-blue-950 to-violet-950 text-white border-b-4 border-orange-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <span class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3.5 py-1 text-xs font-extrabold text-amber-300 mb-4">
            {{ __('messages.proverbs_header_badge') }}
        </span>
        <h1 class="font-serif text-3xl sm:text-5xl font-extrabold text-white mb-4">
            {{ __('messages.proverbs_header_title') }}
        </h1>
        <p class="max-w-3xl text-base sm:text-lg text-slate-200 leading-relaxed">
            {{ __('messages.proverbs_header_subtitle') }}
        </p>

        <form action="{{ route('proverbs.index') }}" method="GET" role="search" aria-label="{{ __('messages.proverbs_search_aria') }}" class="mt-6 max-w-2xl flex flex-col sm:flex-row gap-3">
            @if($category !== '' && $category !== 'all')
                <input type="hidden" name="category" value="{{ $category }}">
            @endif
            <label for="proverbs-search-q" class="sr-only">{{ __('messages.search') }}</label>
            <input
                type="search"
                id="proverbs-search-q"
                name="q"
                value="{{ $search }}"
                placeholder="{{ __('messages.proverbs_search_placeholder') }}"
                class="flex-1 rounded-xl border-2 border-slate-400 bg-white px-4 py-2.5 text-base lg:text-sm font-bold text-slate-950 placeholder:text-slate-600"
            >
            <button type="submit" class="rounded-xl bg-orange-700 px-6 py-2.5 text-sm font-extrabold text-white hover:bg-orange-800 cursor-pointer">
                {{ __('messages.proverbs_search_btn') }}
            </button>
            @if($search !== '' || ($category !== '' && $category !== 'all'))
                <a href="{{ route('proverbs.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-500 bg-slate-900 px-4 py-2.5 text-xs font-extrabold text-amber-300 hover:bg-slate-800">
                    {{ __('messages.proverbs_clear_btn') }}
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
                            <span>{{ __('messages.proverb_contributed_by') }} <strong class="text-slate-950">{{ $proverb->author_name }}</strong></span>
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
                <p class="text-base font-extrabold text-slate-950">{{ __('messages.proverbs_empty_category') }}</p>
            </div>
        @endif
    </section>

    {{-- Formulario para subir un refrán (requiere verificación OAuth anti-spam) --}}
    <section id="subir-refran" class="max-w-3xl mx-auto rounded-2xl border-2 border-slate-900 bg-white p-6 sm:p-10 shadow-lg space-y-6">
        <div>
            <span class="rounded-lg bg-orange-100 px-3 py-1 text-xs font-extrabold text-orange-950">{{ __('messages.verified_participation_badge') }}</span>
            <h2 class="font-serif text-2xl sm:text-3xl font-extrabold text-slate-950 mt-2">
                {{ __('messages.proverb_form_heading') }}
            </h2>
            <p class="text-sm text-slate-800 mt-1">
                {{ __('messages.proverb_form_subheading') }}
            </p>
        </div>

        @error('auth')
            <div class="rounded-xl border border-red-300 bg-red-50 p-3 text-xs font-bold text-red-800">
                {{ $message }}
            </div>
        @enderror

        @if(!session('public_social_identity') && !auth()->check())
            <div class="rounded-xl border-2 border-slate-300 bg-stone-50 p-5 text-center space-y-3">
                <p class="text-sm font-bold text-slate-900">
                    🔒 {{ __('messages.proverb_form_locked_notice') }}
                </p>
                <x-social-identity-banner />
            </div>
        @else
            <x-social-identity-banner />

            <form action="{{ route('proverbs.store') }}" method="POST" x-data="{ submitting: false }" @submit="submitting = true" class="space-y-5">
                @csrf

                <div>
                    <label for="prov-text" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">{{ __('messages.proverb_text_label') }}</label>
                    <textarea id="prov-text" name="text" rows="2" required minlength="5" placeholder="{{ __('messages.proverb_text_placeholder') }}" class="w-full rounded-xl border border-slate-400 px-3.5 py-2.5 text-base font-bold text-slate-950">{{ old('text') }}</textarea>
                    <x-input-error :messages="$errors->get('text')" class="mt-1" />
                </div>

                <div>
                    <label for="prov-meaning" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">{{ __('messages.proverb_meaning_label') }}</label>
                    <textarea id="prov-meaning" name="meaning" rows="3" placeholder="{{ __('messages.proverb_meaning_placeholder') }}" class="w-full rounded-xl border border-slate-400 px-3.5 py-2.5 text-base lg:text-sm font-medium">{{ old('meaning') }}</textarea>
                    <x-input-error :messages="$errors->get('meaning')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="prov-category" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">{{ __('messages.proverb_category_label') }}</label>
                        <x-searchable-select
                            name="category"
                            id="prov-category"
                            :label="__('messages.proverb_category_label')"
                            :options="[
                                'mar' => __('messages.proverbs_cat_mar'),
                                'campo' => __('messages.proverbs_cat_campo'),
                                'vientos' => __('messages.proverbs_cat_vientos'),
                                'fiestas' => __('messages.proverbs_cat_fiestas'),
                                'vida' => __('messages.proverbs_cat_vida'),
                            ]"
                            value="mar"
                            required
                        />
                    </div>

                    <div>
                        <label for="prov-page" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">{{ __('messages.proverb_related_page_label') }}</label>
                        @php
                            $proverbPageOptions = ['' => __('messages.proverb_no_related_page')];
                            foreach($pages as $p) {
                                $proverbPageOptions[(string) $p->id] = $p->getTranslation('title', app()->getLocale());
                            }
                        @endphp
                        <x-searchable-select
                            name="related_page_id"
                            id="prov-page"
                            :label="__('messages.proverb_related_page_label')"
                            :options="$proverbPageOptions"
                            value=""
                            :placeholder="__('messages.proverb_search_page_placeholder')"
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

                <button type="submit" :disabled="submitting" class="w-full rounded-xl bg-orange-700 px-6 py-3.5 text-base font-extrabold text-white shadow-md hover:bg-orange-800 transition cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                    {{ __('messages.proverb_submit_archive_btn') }}
                </button>
            </form>
        @endif
    </section>
</div>
@endsection
