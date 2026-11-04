{{--
    Componente de identificación social ligera obligatoria (Facebook / Google) sin cuentas locales.
    Verifica la autoría en un clic para proteger todos los formularios ciudadanos contra el spam.
--}}
@php
    /** @var array{provider: string, id: string, name: string, email: ?string, avatar: ?string}|null $socialIdentity */
    $socialIdentity = session('public_social_identity');
    $authUser = auth()->user();
@endphp

<div class="rounded-xl border border-slate-300 bg-slate-50 p-4 text-left">
    @if($socialIdentity)
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                @if(!empty($socialIdentity['avatar']))
                    <img src="{{ $socialIdentity['avatar'] }}" alt="{{ $socialIdentity['name'] }}" class="h-10 w-10 rounded-full border border-slate-300 object-cover">
                @else
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-blue-900 font-bold text-white">
                        {{ mb_substr($socialIdentity['name'], 0, 1) }}
                    </span>
                @endif
                <div>
                    <p class="text-sm font-bold text-slate-950">
                        {{ __('messages.social_connected_as', ['name' => $socialIdentity['name'], 'provider' => ucfirst($socialIdentity['provider'])]) }}
                    </p>
                    <p class="text-xs text-slate-700">{{ __('messages.social_verified_session_note') }}</p>
                </div>
            </div>
            <button
                type="button"
                onclick="event.preventDefault(); var f = document.createElement('form'); f.method = 'POST'; f.action = '{{ route('social.disconnect') }}'; var csrf = document.createElement('input'); csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}'; f.appendChild(csrf); document.body.appendChild(f); f.submit();"
                class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-900 hover:bg-slate-100 cursor-pointer"
            >
                {{ __('messages.social_disconnect') }}
            </button>
        </div>
        <input type="hidden" name="author_name" value="{{ $socialIdentity['name'] }}">
        @if(!empty($socialIdentity['email']))
            <input type="hidden" name="author_email" value="{{ $socialIdentity['email'] }}">
        @endif
    @elseif($authUser)
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-blue-900 font-bold text-white">
                    {{ mb_substr($authUser->name, 0, 1) }}
                </span>
                <div>
                    <p class="text-sm font-bold text-slate-950">
                        {{ __('messages.social_connected_as', ['name' => $authUser->name, 'provider' => 'Desde Chipiona']) }}
                    </p>
                    <p class="text-xs text-slate-700">{{ __('messages.social_verified_session_note') }}</p>
                </div>
            </div>
        </div>
        <input type="hidden" name="author_name" value="{{ $authUser->name }}">
        <input type="hidden" name="author_email" value="{{ $authUser->email }}">
    @else
        <div class="space-y-3">
            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                <div>
                    <p class="text-sm font-bold text-slate-950">{{ __('messages.social_required_title') }}</p>
                    <p class="text-xs text-slate-700 mt-0.5">{{ __('messages.social_required_desc') }}</p>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    <a
                        href="{{ route('social.redirect', ['provider' => 'facebook']) }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#145dbf] px-3.5 py-2 text-xs font-bold text-white shadow-xs hover:bg-[#0f4896]"
                    >
                        <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/></svg>
                        <span>{{ __('messages.social_connect_facebook') }}</span>
                    </a>
                    <a
                        href="{{ route('social.redirect', ['provider' => 'google']) }}"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-bold text-slate-900 shadow-xs hover:bg-slate-100"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" aria-hidden="true"><path fill="#EA4335" d="M12 10.2v3.9h5.5c-.2 1.3-1.5 3.8-5.5 3.8-3.3 0-6-2.7-6-6s2.7-6 6-6c1.9 0 3.1.8 3.8 1.5l2.6-2.5C16.8 3.4 14.6 2.5 12 2.5 6.8 2.5 2.5 6.8 2.5 12s4.3 9.5 9.5 9.5c5.5 0 9.1-3.9 9.1-9.3 0-.6-.1-1.1-.2-1.6H12z"/></svg>
                        <span>{{ __('messages.social_connect_google') }}</span>
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
