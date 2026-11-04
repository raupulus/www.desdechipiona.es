<div role="group" aria-label="{{ __('messages.language_switcher') }}" class="flex items-center space-x-1 text-sm">
    @foreach(config('locales.available', ['es', 'en']) as $locale)
        @php
            $isActive = app()->getLocale() === $locale;
            $label = $locale === 'es' ? 'Español' : ($locale === 'en' ? 'English' : strtoupper($locale));
        @endphp
        <a
            href="{{ route('language.switch', $locale) }}"
            hreflang="{{ $locale }}"
            lang="{{ $locale }}"
            aria-label="{{ $label }}"
            @if($isActive) aria-current="true" @endif
            class="px-2.5 py-1 min-h-6 inline-flex items-center rounded-md text-xs transition {{ $isActive ? 'bg-orange-700 text-white font-bold' : 'text-slate-200 hover:bg-slate-800 hover:text-white font-semibold' }}"
        >{{ strtoupper($locale) }}</a>
    @endforeach
</div>
