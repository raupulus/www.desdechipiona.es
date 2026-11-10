@props([
    'variant' => 'horizontal',
    'adSlot' => null,
])

@php
    // Prohibición estricta en portada: jamás renderizar anuncios en la página principal
    $isHomeRoute = request()->routeIs('home', 'explore') || request()->is('/', 'explore', 'explorar');

    $isEnabled = (bool) config('services.adsense.enabled', false)
        || filter_var(setting('adsense_enabled', false), FILTER_VALIDATE_BOOLEAN);

    $isPreview = (bool) config('services.adsense.preview', false);

    $clientId = trim((string) (setting('adsense_client_id') ?: config('services.adsense.client_id', '')));

    $defaultSlot = $variant === 'sidebar'
        ? (setting('adsense_slot_sidebar') ?: config('services.adsense.slot_sidebar', ''))
        : (setting('adsense_slot_horizontal') ?: config('services.adsense.slot_horizontal', ''));

    $slotId = trim((string) ($adSlot ?: $defaultSlot));

    $canRenderLiveAd = ! $isHomeRoute && $isEnabled && $clientId !== '' && $slotId !== '';
    $canRenderPreview = ! $isHomeRoute && ! $canRenderLiveAd && ($isPreview || ($isEnabled && ($clientId === '' || $slotId === '')));
@endphp

@if($canRenderLiveAd || $canRenderPreview)
    @if($canRenderLiveAd)
        @once
            @push('head_js')
                <script>
                    (window.adsbygoogle = window.adsbygoogle || []).requestNonPersonalizedAds =
                        localStorage.getItem('chipiona_cookie_consent') === 'all' ? 0 : 1;

                    window.chipionaBootAdsense = function (consentChoice) {
                        var consent = consentChoice || localStorage.getItem('chipiona_cookie_consent');
                        (window.adsbygoogle = window.adsbygoogle || []).requestNonPersonalizedAds = consent === 'all' ? 0 : 1;

                        if (!document.getElementById('chipiona-adsbygoogle-script')) {
                            var s = document.createElement('script');
                            s.id = 'chipiona-adsbygoogle-script';
                            s.async = true;
                            s.src = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ urlencode($clientId) }}';
                            s.crossOrigin = 'anonymous';
                            document.head.appendChild(s);
                        }

                        var pushSlots = function () {
                            document.querySelectorAll('ins.adsbygoogle[data-chipiona-pushed="0"]').forEach(function (ins) {
                                if (ins.offsetParent === null) {
                                    return;
                                }
                                ins.setAttribute('data-chipiona-pushed', '1');
                                try {
                                    (window.adsbygoogle = window.adsbygoogle || []).push({});
                                } catch (e) {}
                            });
                        };

                        if (typeof window.requestAnimationFrame === 'function') {
                            window.requestAnimationFrame(function () {
                                setTimeout(pushSlots, 0);
                            });
                        } else {
                            setTimeout(pushSlots, 0);
                        }
                    };

                    document.addEventListener('DOMContentLoaded', function () {
                        window.chipionaBootAdsense();
                    });
                </script>
                <script
                    id="chipiona-adsbygoogle-script"
                    async
                    src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ urlencode($clientId) }}"
                    crossorigin="anonymous"
                ></script>
            @endpush
        @endonce
    @endif

    <aside
        aria-label="{{ __('messages.ad_region_aria') }}"
        data-ad-variant="{{ $variant }}"
        {{ $attributes->merge([
            'class' => 'rounded-2xl border border-slate-200/80 bg-stone-100/70 px-4 py-3 shadow-2xs',
        ]) }}
    >
        <div class="mb-2 flex items-center justify-between text-[10px] font-bold uppercase tracking-widest text-slate-400 select-none">
            <span>{{ __('messages.ad_label') }}</span>
            <span aria-hidden="true">·</span>
        </div>

        @if($variant === 'sidebar')
            <div class="relative mx-auto flex h-[250px] w-full max-w-[300px] items-center justify-center overflow-hidden rounded-xl bg-white/80">
                @if($canRenderLiveAd)
                    <ins
                        class="adsbygoogle"
                        style="display:inline-block;width:300px;height:250px"
                        data-ad-client="{{ $clientId }}"
                        data-ad-slot="{{ $slotId }}"
                        data-chipiona-pushed="0"
                    ></ins>
                @else
                    <div class="flex h-full w-full flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-stone-50/90 p-4 text-center">
                        <span class="font-mono text-xs font-extrabold text-slate-500">300 × 250</span>
                        <span class="mt-1 text-[11px] font-medium text-slate-400 leading-snug">
                            {{ __('messages.ad_preview_sidebar_desc') }}
                        </span>
                    </div>
                @endif
            </div>
        @else
            <div class="relative mx-auto flex h-[90px] w-full max-w-[728px] items-center justify-center overflow-hidden rounded-xl bg-white/80">
                @if($canRenderLiveAd)
                    <ins
                        class="adsbygoogle"
                        style="display:inline-block;width:100%;max-width:728px;height:90px"
                        data-ad-client="{{ $clientId }}"
                        data-ad-slot="{{ $slotId }}"
                        data-chipiona-pushed="0"
                    ></ins>
                @else
                    <div class="flex h-full w-full flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-stone-50/90 px-4 py-2 text-center">
                        <span class="font-mono text-xs font-extrabold text-slate-500">728 × 90</span>
                        <span class="mt-0.5 text-[11px] font-medium text-slate-400 leading-snug">
                            {{ __('messages.ad_preview_horizontal_desc') }}
                        </span>
                    </div>
                @endif
            </div>
        @endif
    </aside>
@endif
