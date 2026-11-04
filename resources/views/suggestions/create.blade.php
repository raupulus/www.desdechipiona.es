@extends('layouts.app')

@section('content')
<header class="bg-gradient-to-br from-slate-950 via-blue-950 to-violet-950 text-white border-b-4 border-orange-700">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <span class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3.5 py-1 text-xs font-extrabold text-amber-300 mb-4">
            {{ __('messages.suggestions_header_badge') }}
        </span>
        <h1 class="font-serif text-3xl sm:text-5xl font-extrabold text-white mb-4">
            {{ __('messages.suggestions_header_title') }}
        </h1>
        <p class="text-base sm:text-lg text-slate-200 leading-relaxed">
            {{ __('messages.suggestions_header_subtitle') }}
        </p>
    </div>
</header>

<section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="rounded-2xl border-2 border-slate-200 bg-white p-6 sm:p-10 shadow-sm space-y-6">
        @error('auth')
            <div class="rounded-xl border border-red-300 bg-red-50 p-3 text-xs font-bold text-red-800">
                {{ $message }}
            </div>
        @enderror

        @if(!session('public_social_identity') && !auth()->check())
            <div class="rounded-xl border-2 border-slate-300 bg-stone-50 p-6 text-center space-y-3">
                <p class="text-sm font-bold text-slate-900">
                    🔒 {{ __('messages.suggestion_form_locked_notice') }}
                </p>
                <x-social-identity-banner />
            </div>
        @else
            <x-social-identity-banner />

            <form action="{{ route('suggestions.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="sugg-group" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">{{ __('messages.sugg_step1_label') }}</label>
                        @php
                            $groupOptions = [];
                            foreach($groups as $group) {
                                $groupOptions[$group->value] = $group->label();
                            }
                        @endphp
                        <x-searchable-select
                            name="content_group"
                            id="sugg-group"
                            :options="$groupOptions"
                            :value="old('content_group', $groups[0]->value ?? 'historia')"
                            required
                        />
                    </div>

                    <div>
                        <label for="sugg-related" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">{{ __('messages.sugg_step2_label') }}</label>
                        @php
                            $pageOptions = ['' => __('messages.sugg_independent_topic')];
                            foreach($pages as $p) {
                                $pageOptions[(string) $p->id] = $p->getTranslation('title', app()->getLocale());
                            }
                        @endphp
                        <x-searchable-select
                            name="related_page_id"
                            id="sugg-related"
                            :options="$pageOptions"
                            :value="old('related_page_id')"
                            :placeholder="__('messages.proverb_search_page_placeholder')"
                        />
                    </div>
                </div>

                <div>
                    <label for="sugg-title" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">{{ __('messages.sugg_step3_label') }}</label>
                    <input type="text" id="sugg-title" name="title" value="{{ old('title') }}" required minlength="4" maxlength="255" placeholder="{{ __('messages.sugg_step3_placeholder') }}" class="w-full rounded-xl border border-slate-300 px-3.5 py-3 text-base sm:text-sm font-bold text-slate-950">
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div>
                    <label for="sugg-body" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">{{ __('messages.sugg_step4_label') }}</label>
                    <textarea id="sugg-body" name="body" rows="9" required minlength="20" placeholder="{{ __('messages.sugg_step4_placeholder') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-base sm:text-sm font-medium text-slate-950 leading-relaxed">{{ old('body') }}</textarea>
                    <x-input-error :messages="$errors->get('body')" class="mt-1" />
                </div>

                <div>
                    <label for="sugg-sources" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">{{ __('messages.sugg_step5_label') }}</label>
                    <textarea id="sugg-sources" name="sources_info" rows="2" placeholder="{{ __('messages.sugg_step5_placeholder') }}" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-base sm:text-sm font-medium">{{ old('sources_info') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="sugg-location" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">{{ __('messages.sugg_location_label') }}</label>
                        <input type="text" id="sugg-location" name="location_name" value="{{ old('location_name') }}" placeholder="{{ __('messages.sugg_location_placeholder') }}" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-base sm:text-sm">
                    </div>
                    <div>
                        <label for="sugg-lat" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">{{ __('messages.sugg_lat_label') }}</label>
                        <input type="number" step="0.000001" id="sugg-lat" name="latitude" value="{{ old('latitude') }}" placeholder="36.7378" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-base sm:text-sm">
                    </div>
                    <div>
                        <label for="sugg-lng" class="block text-xs font-extrabold uppercase text-slate-900 mb-1">{{ __('messages.sugg_lng_label') }}</label>
                        <input type="number" step="0.000001" id="sugg-lng" name="longitude" value="{{ old('longitude') }}" placeholder="-6.4425" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-base sm:text-sm">
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
                    {{ __('messages.sugg_submit_team_btn') }}
                </button>
            </form>
        @endif
    </div>
</section>
@endsection
