{{-- Barra superior de accesibilidad, autoría verificada y canales oficiales --}}
<div class="bg-slate-950 text-slate-100 border-b border-slate-800 text-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-3">
            <span class="inline-flex items-center gap-1.5 font-semibold text-amber-300">
                <span aria-hidden="true">🦎</span>
                <span>{{ __('messages.top_bar_author') }} <a href="https://raupulus.dev" target="_blank" rel="noopener noreferrer" class="underline hover:text-white font-bold">Raúl Caro Pastorino</a></span>
            </span>
            <span class="hidden md:inline text-slate-500" aria-hidden="true">·</span>
            <a href="https://t.me/desdechipiona" target="_blank" rel="noopener noreferrer" class="hidden sm:inline-flex items-center gap-1 text-sky-300 hover:text-white font-semibold">
                <span>{{ __('messages.top_bar_telegram') }}</span>
            </a>
        </div>

        <div class="flex items-center gap-3">
            {{-- Selector de tamaño de fuente accesible (A / A+ / A++) para lectores mayores --}}
            <div class="inline-flex items-center rounded-lg border border-slate-700 bg-slate-900 p-0.5" role="group" aria-label="{{ __('messages.font_size_label') }}">
                <button
                    type="button"
                    @click="setFontScale('normal')"
                    :aria-pressed="(fontScale === 'normal').toString()"
                    :class="fontScale === 'normal' ? 'bg-orange-700 text-white' : 'text-slate-200 hover:text-white'"
                    class="px-2.5 py-1 min-h-6 rounded-md font-bold text-xs transition"
                    title="{{ __('messages.font_size_normal_title') }}"
                >A</button>
                <button
                    type="button"
                    @click="setFontScale('large')"
                    :aria-pressed="(fontScale === 'large').toString()"
                    :class="fontScale === 'large' ? 'bg-orange-700 text-white' : 'text-slate-200 hover:text-white'"
                    class="px-2.5 py-1 min-h-6 rounded-md font-bold text-xs transition"
                    title="{{ __('messages.font_size_large_title') }}"
                >A+</button>
                <button
                    type="button"
                    @click="setFontScale('xlarge')"
                    :aria-pressed="(fontScale === 'xlarge').toString()"
                    :class="fontScale === 'xlarge' ? 'bg-orange-700 text-white' : 'text-slate-200 hover:text-white'"
                    class="px-2.5 py-1 min-h-6 rounded-md font-bold text-xs transition"
                    title="{{ __('messages.font_size_xlarge_title') }}"
                >A++</button>
            </div>

            <x-language-switcher />

            @auth
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('filament.admin.pages.dashboard') }}" class="font-bold text-amber-300 hover:text-white">Intranet</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-slate-300 hover:text-white">{{ __('messages.logout') }}</button>
                </form>
            @endauth
        </div>
    </div>
</div>

{{-- Navegación Principal de Desde Chipiona --}}
<nav
    aria-label="{{ __('messages.nav_primary') }}"
    class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-xs"
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    @click.outside="open = false"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20 gap-3">
            {{-- Marca oficial con medallón heráldico --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 group min-w-0 shrink">
                <img
                    src="{{ asset('images/logo.webp') }}"
                    alt="{{ setting('site_title', config('app.name')) }}"
                    width="48"
                    height="48"
                    class="h-11 w-11 sm:h-12 sm:w-12 shrink-0 rounded-full border-2 border-amber-500/70 shadow-sm object-cover"
                >
                <div class="min-w-0">
                    <span class="block truncate font-serif text-lg sm:text-xl font-extrabold tracking-tight text-slate-950 group-hover:text-orange-800 transition">
                        {{ setting('site_title', config('app.name')) }}
                    </span>
                    <span class="hidden sm:block truncate text-xs font-semibold text-slate-700">
                        {{ __('messages.brand_subtitle') }}
                    </span>
                </div>
            </a>

            {{-- Enlaces de escritorio --}}
            <div class="hidden lg:flex lg:items-center lg:gap-1">
                <a
                    href="{{ route('home') }}"
                    @if(request()->routeIs('home') || request()->routeIs('explore')) aria-current="page" @endif
                    class="whitespace-nowrap px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->routeIs('home') || request()->routeIs('explore') ? 'bg-orange-700 text-white shadow-xs' : 'text-slate-900 hover:bg-slate-100' }}"
                >
                    {{ __('messages.explore_all') }}
                </a>
                <a
                    href="{{ route('news.index') }}"
                    @if(request()->routeIs('news.*')) aria-current="page" @endif
                    class="whitespace-nowrap px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->routeIs('news.*') ? 'bg-slate-950 text-white' : 'text-slate-900 hover:bg-slate-100' }}"
                >
                    {{ __('messages.news_nav') }}
                </a>
                <a
                    href="{{ route('proverbs.index') }}"
                    @if(request()->routeIs('proverbs.*')) aria-current="page" @endif
                    class="whitespace-nowrap px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->routeIs('proverbs.*') ? 'bg-slate-950 text-white' : 'text-slate-900 hover:bg-slate-100' }}"
                >
                    {{ __('messages.proverbs_nav') }}
                </a>
                <a
                    href="{{ route('weather.index') }}"
                    @if(request()->routeIs('weather.*')) aria-current="page" @endif
                    class="whitespace-nowrap px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->routeIs('weather.*') ? 'bg-slate-950 text-white' : 'text-slate-900 hover:bg-slate-100' }}"
                >
                    {{ __('messages.weather_tides_nav') }}
                </a>
                <a
                    href="{{ route('phones.index') }}"
                    @if(request()->routeIs('phones.*')) aria-current="page" @endif
                    class="whitespace-nowrap px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->routeIs('phones.*') ? 'bg-slate-950 text-white' : 'text-slate-900 hover:bg-slate-100' }}"
                >
                    {{ __('messages.phones_nav') }}
                </a>
                <a
                    href="{{ route('legal') }}"
                    @if(request()->routeIs('legal') || request()->routeIs('about')) aria-current="page" @endif
                    class="whitespace-nowrap px-3 py-2 rounded-xl text-sm font-bold transition {{ request()->routeIs('legal') || request()->routeIs('about') ? 'bg-slate-950 text-white' : 'text-slate-900 hover:bg-slate-100' }}"
                >
                    {{ __('messages.author_legal_nav') }}
                </a>
            </div>

            {{-- CTA Aportar Contenido --}}
            <div class="hidden lg:flex lg:items-center lg:gap-2 shrink-0">
                <a
                    href="{{ route('suggestions.create') }}"
                    class="whitespace-nowrap inline-flex items-center gap-2 rounded-xl bg-violet-800 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-violet-900 transition"
                >
                    <span aria-hidden="true">✍️</span>
                    <span>{{ __('messages.contribute_btn') }}</span>
                </a>
            </div>

            {{-- Botón móvil --}}
            <div class="flex items-center lg:hidden shrink-0">
                <button
                    type="button"
                    @click="open = !open"
                    :aria-expanded="open.toString()"
                    aria-controls="mobile-menu"
                    :aria-label="open ? '{{ __('messages.menu_close') }}' : '{{ __('messages.menu_open') }}'"
                    class="p-2.5 text-slate-900 hover:bg-slate-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-700"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Menú desplegable móvil --}}
    <div id="mobile-menu" x-cloak x-show="open" class="lg:hidden max-h-[calc(100dvh-5rem)] overflow-y-auto overscroll-contain border-t border-slate-200 bg-white">
        <div class="px-4 py-4 space-y-1.5">
            <a
                href="{{ route('home') }}"
                @if(request()->routeIs('home') || request()->routeIs('explore')) aria-current="page" @endif
                class="block px-3 py-2 rounded-lg font-bold transition {{ request()->routeIs('home') || request()->routeIs('explore') ? 'bg-orange-700 text-white' : 'text-slate-950 hover:bg-slate-100' }}"
            >{{ __('messages.explore_all') }}</a>
            <a
                href="{{ route('news.index') }}"
                @if(request()->routeIs('news.*')) aria-current="page" @endif
                class="block px-3 py-2 rounded-lg font-bold transition {{ request()->routeIs('news.*') ? 'bg-slate-950 text-white' : 'text-slate-950 hover:bg-slate-100' }}"
            >{{ __('messages.news_nav') }}</a>
            <a
                href="{{ route('proverbs.index') }}"
                @if(request()->routeIs('proverbs.*')) aria-current="page" @endif
                class="block px-3 py-2 rounded-lg font-bold transition {{ request()->routeIs('proverbs.*') ? 'bg-slate-950 text-white' : 'text-slate-950 hover:bg-slate-100' }}"
            >{{ __('messages.proverbs_nav') }}</a>
            <a
                href="{{ route('weather.index') }}"
                @if(request()->routeIs('weather.*')) aria-current="page" @endif
                class="block px-3 py-2 rounded-lg font-bold transition {{ request()->routeIs('weather.*') ? 'bg-slate-950 text-white' : 'text-slate-950 hover:bg-slate-100' }}"
            >{{ __('messages.weather_tides_nav') }}</a>
            <a
                href="{{ route('phones.index') }}"
                @if(request()->routeIs('phones.*')) aria-current="page" @endif
                class="block px-3 py-2 rounded-lg font-bold transition {{ request()->routeIs('phones.*') ? 'bg-slate-950 text-white' : 'text-slate-950 hover:bg-slate-100' }}"
            >{{ __('messages.phones_nav') }}</a>
            <a
                href="{{ route('blog.index') }}"
                @if(request()->routeIs('blog.*')) aria-current="page" @endif
                class="block px-3 py-2 rounded-lg font-bold transition {{ request()->routeIs('blog.*') ? 'bg-slate-950 text-white' : 'text-slate-950 hover:bg-slate-100' }}"
            >{{ __('messages.blog') }}</a>
            <a
                href="{{ route('legal') }}"
                @if(request()->routeIs('legal') || request()->routeIs('about')) aria-current="page" @endif
                class="block px-3 py-2 rounded-lg font-bold transition {{ request()->routeIs('legal') || request()->routeIs('about') ? 'bg-slate-950 text-white' : 'text-slate-950 hover:bg-slate-100' }}"
            >{{ __('messages.author_legal_nav') }}</a>
            <a
                href="{{ route('contact') }}"
                @if(request()->routeIs('contact')) aria-current="page" @endif
                class="block px-3 py-2 rounded-lg font-bold transition {{ request()->routeIs('contact') ? 'bg-slate-950 text-white' : 'text-slate-950 hover:bg-slate-100' }}"
            >{{ __('messages.contact') }}</a>
            <div class="pt-2">
                <a href="{{ route('suggestions.create') }}" class="flex items-center justify-center gap-2 w-full rounded-xl bg-violet-800 px-4 py-2.5 text-sm font-bold text-white hover:bg-violet-900 transition">
                    <span>✍️ {{ __('messages.contribute_btn') }}</span>
                </a>
            </div>
        </div>
    </div>
</nav>
