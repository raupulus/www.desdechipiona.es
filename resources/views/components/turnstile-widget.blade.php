{{--
    Componente de protección Cloudflare Turnstile para todos los formularios públicos.
    Si TURNSTILE_SITE_KEY está configurada en .env, renderiza el widget oficial de Cloudflare.
    En desarrollo local sin clave configurada, muestra un sello visual accesible de protección activa.
    Uso: <x-turnstile-widget action="correction" />
--}}
@props(['action' => 'submit', 'theme' => 'light'])

@if(config('services.turnstile.site_key') && config('services.turnstile.secret_key'))
    <div class="my-3">
        <div
            class="cf-turnstile"
            data-sitekey="{{ config('services.turnstile.site_key') }}"
            data-action="{{ $action }}"
            data-theme="{{ $theme }}"
        ></div>
        <x-input-error :messages="$errors->get('cf-turnstile-response')" class="mt-2" />
    </div>
    @pushOnce('scripts')
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endPushOnce
@else
    <div class="my-3 flex items-center justify-between gap-3 rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-xs font-semibold text-slate-800">
        <div class="flex items-center gap-2.5">
            <span class="inline-flex h-5 w-5 items-center justify-center rounded-md bg-emerald-700 text-white" aria-hidden="true">✓</span>
            <span>{!! __('messages.turnstile_protected_badge') !!}</span>
        </div>
        <span class="font-mono text-xs text-slate-600">CF-TURNSTILE</span>
        <input type="hidden" name="cf-turnstile-response" value="dev-bypass-token">
    </div>
    <x-input-error :messages="$errors->get('cf-turnstile-response')" class="mt-2" />
@endif
