@php
    $seoInstance = isset($seo) && $seo instanceof \App\Services\SeoService ? $seo : app(\App\Services\SeoService::class);
    $seoData = $seoInstance->toArray();
    $pageTitle = $seoData['title'] ?? setting('site_title', config('app.name'));
    $pageDescription = $seoData['description'] ?? setting('site_description', '');
    $pageImage = $seoData['image'] ?? asset('images/redes-sociales/og-image.webp');
    $pageImageWidth = $seoData['imageWidth'] ?? 1200;
    $pageImageHeight = $seoData['imageHeight'] ?? 630;
    $baseUrl = $seoData['baseUrl'] ?? url()->current();
    $pageUrl = $seoData['url'] ?? url()->current();
    $pageType = $seoData['type'] ?? 'website';
    $isErrorPage = isset($exception) && $exception instanceof \Throwable;
    $pageRobots = $isErrorPage ? 'noindex,nofollow' : ($seoData['robots'] ?? 'index,follow');
    $currentLocale = app()->getLocale();
    $availableLocales = config('locales.available', ['es', 'en']);
    $twitterHandle = setting('twitter_url') ? '@'.basename(rtrim((string) setting('twitter_url'), '/')) : null;

    $adsenseClientId = trim((string) (setting('adsense_client_id') ?: config('services.adsense.client_id', '')));
    $adsenseEnabled = (bool) config('services.adsense.enabled', false)
        || filter_var(setting('adsense_enabled', false), FILTER_VALIDATE_BOOLEAN);
    $adsenseCmpEnabled = (bool) config('services.adsense.cmp_enabled', true);

    $adsensePublisherId = '';
    $adsenseAccountMeta = '';
    if ($adsenseClientId !== '') {
        $adsensePublisherId = preg_replace('/^ca-/i', '', $adsenseClientId) ?? $adsenseClientId;
        if (! str_starts_with(strtolower($adsensePublisherId), 'pub-')) {
            $adsensePublisherId = 'pub-'.ltrim($adsensePublisherId, '-');
        }
        $adsenseAccountMeta = 'ca-'.$adsensePublisherId;
    }
@endphp
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta name="theme-color" content="#020617" />
@if($adsenseAccountMeta !== '')
<meta name="google-adsense-account" content="{{ $adsenseAccountMeta }}" />
@endif
<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDescription }}" />
<meta name="author" content="{{ $seoData['author'] ?? 'Raúl Caro Pastorino' }}" />
<meta name="robots" content="{{ $pageRobots }}" />
@unless($isErrorPage)
@php
    $pageParam = request()->query('page');
    $pageSuffix = ($pageParam && is_numeric($pageParam) && (int) $pageParam > 1) ? '&page='.(int) $pageParam : '';
    $defaultPageSuffix = ($pageParam && is_numeric($pageParam) && (int) $pageParam > 1) ? '?page='.(int) $pageParam : '';
@endphp
<link rel="canonical" href="{{ $pageUrl }}" />
@foreach($availableLocales as $loc)
<link rel="alternate" hreflang="{{ $loc }}" href="{{ $baseUrl }}?lang={{ $loc }}{{ $pageSuffix }}" />
@endforeach
<link rel="alternate" hreflang="x-default" href="{{ $baseUrl }}{{ $defaultPageSuffix }}" />
@endunless
<meta property="og:locale" content="{{ $currentLocale === 'es' ? 'es_ES' : 'en_US' }}" />
@foreach($availableLocales as $loc)
@if($loc !== $currentLocale)
<meta property="og:locale:alternate" content="{{ $loc === 'es' ? 'es_ES' : 'en_US' }}" />
@endif
@endforeach
<meta property="og:title" content="{{ $pageTitle }}" />
<meta property="og:site_name" content="{{ setting('site_title', config('app.name')) }}" />
<meta property="og:type" content="{{ $pageType }}" />
<meta property="og:description" content="{{ $pageDescription }}" />
<meta property="og:image" content="{{ $pageImage }}" />
<meta property="og:image:type" content="image/webp" />
<meta property="og:image:width" content="{{ $pageImageWidth }}" />
<meta property="og:image:height" content="{{ $pageImageHeight }}" />
<meta property="og:image:alt" content="{{ $pageTitle }}" />
<meta property="og:url" content="{{ $pageUrl }}" />
<meta name="twitter:card" content="summary_large_image" />
@if($twitterHandle)
<meta name="twitter:site" content="{{ $twitterHandle }}" />
@endif
<meta name="twitter:title" content="{{ $pageTitle }}" />
<meta name="twitter:description" content="{{ $pageDescription }}" />
<meta name="twitter:image" content="{{ $pageImage }}" />
<meta name="twitter:image:alt" content="{{ $pageTitle }}" />
@if($pageType === 'article')
@if(!empty($seoData['publishedAt']))
<meta property="article:published_time" content="{{ $seoData['publishedAt'] }}" />
@endif
@if(!empty($seoData['modifiedAt']))
<meta property="article:modified_time" content="{{ $seoData['modifiedAt'] }}" />
@endif
@if(!empty($seoData['author']))
<meta property="article:author" content="{{ $seoData['author'] }}" />
@endif
@endif
@unless($isErrorPage)
<script type="application/ld+json">
{!! json_encode($seoInstance->toJsonLd(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endunless
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logos/logo-180x180.webp') }}" />
<link rel="icon" type="image/webp" sizes="64x64" href="{{ asset('images/logos/logo-64x64.webp') }}" />
<link rel="icon" type="image/webp" sizes="32x32" href="{{ asset('images/logos/logo-32x32.webp') }}" />
<link rel="icon" type="image/webp" sizes="16x16" href="{{ asset('images/logos/logo-16x16.webp') }}" />
<link rel="manifest" href="{{ asset('site.webmanifest') }}" />
@if($adsenseEnabled && $adsensePublisherId !== '')
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    (function(){
        var stored = localStorage.getItem('chipiona_cookie_consent');
        var state = stored === 'all' ? 'granted' : 'denied';
        gtag('consent', 'default', {
            ad_storage: state,
            ad_user_data: state,
            ad_personalization: state,
            analytics_storage: 'denied',
            functionality_storage: 'granted',
            security_storage: 'granted',
            wait_for_update: 500
        });
        gtag('set', 'ads_data_redaction', true);
        (window.adsbygoogle = window.adsbygoogle || []).requestNonPersonalizedAds = stored === 'all' ? 0 : 1;
    })();
</script>
@if($adsenseCmpEnabled)
<script async src="https://fundingchoicesmessages.google.com/i/{{ urlencode($adsensePublisherId) }}?ers=1"></script>
<script>(function(){function signalGooglefcPresent(){if(!window.frames['googlefcPresent']){if(document.body){var iframe=document.createElement('iframe');iframe.style='width:0;height:0;border:none;z-index:-1000;left:-1000px;top:-1000px;';iframe.style.display='none';iframe.name='googlefcPresent';document.body.appendChild(iframe);}else{setTimeout(signalGooglefcPresent,0);}}}signalGooglefcPresent();})();</script>
@endif
@endif
