<footer class="bg-slate-950 text-slate-200 mt-20 border-t-4 border-orange-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
            {{-- Columna 1: Identidad y Autoría --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.webp') }}" alt="{{ setting('site_title', config('app.name')) }}" width="44" height="44" class="h-11 w-11 rounded-full border border-amber-400">
                    <div>
                        <h2 class="font-serif text-lg font-extrabold text-white">{{ setting('site_title', config('app.name')) }}</h2>
                        <p class="text-xs text-amber-300 font-semibold">https://desdechipiona.es</p>
                    </div>
                </div>
                <p class="text-sm text-slate-300 leading-relaxed">
                    {{ __('messages.footer_description') }}
                </p>
                <div class="rounded-xl border border-slate-800 bg-slate-900 p-3.5 text-xs space-y-1">
                    <p class="font-bold text-white">{{ __('messages.author_legal_nav') }}</p>
                    <p class="text-slate-300">
                        {{ __('messages.footer_author_note') }} <strong>Raúl Caro Pastorino</strong> (<a href="https://raupulus.dev" target="_blank" rel="noopener noreferrer" class="underline text-sky-300 hover:text-white">raupulus.dev</a> · <code class="text-amber-300">public@raupulus.dev</code>).
                    </p>
                </div>
            </div>

            {{-- Columna 2: Explorar Chipiona --}}
            <div>
                <h2 class="text-white text-sm font-extrabold uppercase tracking-wider mb-4">{{ __('messages.footer_explore_heading') }}</h2>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('home', ['group' => 'historia']) }}" class="text-slate-300 hover:text-white">🏛️ Historia Documentada</a></li>
                    <li><a href="{{ route('home', ['group' => 'monumentos']) }}" class="text-slate-300 hover:text-white">🗼 Faro y Monumentos</a></li>
                    <li><a href="{{ route('home', ['group' => 'playas']) }}" class="text-slate-300 hover:text-white">🏖️ Playas y Corrales de Pesca</a></li>
                    <li><a href="{{ route('home', ['group' => 'fauna']) }}" class="text-slate-300 hover:text-white">🦎 Camaleón y Fauna</a></li>
                    <li><a href="{{ route('home', ['group' => 'gastronomia']) }}" class="text-slate-300 hover:text-white">🍷 Moscatel y Recetas</a></li>
                    <li><a href="{{ route('proverbs.index') }}" class="text-slate-300 hover:text-white">🗣️ Refranero Popular</a></li>
                </ul>
            </div>

            {{-- Columna 3: Servicios y Participación --}}
            <div>
                <h2 class="text-white text-sm font-extrabold uppercase tracking-wider mb-4">{{ __('messages.footer_services_heading') }}</h2>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('news.index') }}" class="text-slate-300 hover:text-white">📰 {{ __('messages.news_nav') }}</a></li>
                    <li><a href="{{ route('weather.index') }}" class="text-slate-300 hover:text-white">🌊 {{ __('messages.weather_tides_nav') }}</a></li>
                    <li><a href="{{ route('phones.index') }}" class="text-slate-300 hover:text-white">📞 {{ __('messages.phones_nav') }}</a></li>
                    <li><a href="{{ route('suggestions.create') }}" class="text-slate-300 hover:text-white">✍️ {{ __('messages.contribute_btn') }}</a></li>
                    <li><a href="{{ route('blog.index') }}" class="text-slate-300 hover:text-white">{{ __('messages.blog') }}</a></li>
                    <li><a href="{{ route('contact') }}" class="text-slate-300 hover:text-white">{{ __('messages.contact') }} (info@desdechipiona.es)</a></li>
                </ul>
            </div>

            {{-- Columna 4: Páginas Legales y Licencia --}}
            <div>
                <h2 class="text-white text-sm font-extrabold uppercase tracking-wider mb-4">{{ __('messages.footer_links_legal_heading') }}</h2>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('legal') }}" class="text-slate-300 hover:text-white">⚖️ Autoría y Aviso Legal</a></li>
                    <li><a href="{{ route('about') }}" class="text-slate-300 hover:text-white">{{ __('messages.about') }}</a></li>
                    <li><a href="{{ route('privacy') }}" class="text-slate-300 hover:text-white">{{ __('messages.privacy') }}</a></li>
                    <li><a href="{{ route('cookies') }}" class="text-slate-300 hover:text-white">{{ __('messages.cookies') }}</a></li>
                    <li><a href="https://t.me/desdechipiona" target="_blank" rel="noopener noreferrer" class="text-sky-300 hover:text-white font-semibold">📢 Canal de Telegram</a></li>
                </ul>
                <div class="mt-4 rounded-xl border border-slate-800 bg-slate-900 p-3 text-xs text-slate-300">
                    Código libre bajo licencia <strong>GNU AGPLv3</strong>. Protegido con <strong>Cloudflare Turnstile</strong>.
                </div>
            </div>
        </div>

        <div class="border-t border-slate-800 mt-10 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-300">
            <p>&copy; {{ date('Y') }} {{ setting('site_title', config('app.name')) }} · Raúl Caro Pastorino. {{ __('messages.all_rights_reserved') }}.</p>
            <p>Contacto: <a href="mailto:info@desdechipiona.es" class="underline hover:text-white">info@desdechipiona.es</a></p>
        </div>
    </div>
</footer>
