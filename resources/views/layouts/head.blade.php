@php
    $seoInstance = isset($seo) && $seo instanceof \App\Services\SeoService ? $seo : app(\App\Services\SeoService::class);
    $seoData = $seoInstance->toArray();
    $pageTitle = $seoData['title'] ?? setting('site_title', config('app.name'));
    $pageDescription = $seoData['description'] ?? setting('site_description', '');
    $pageImage = $seoData['image'] ?? asset('images/redes-sociales/og-image.webp');
    $baseUrl = $seoData['baseUrl'] ?? url()->current();
    $pageUrl = $seoData['url'] ?? url()->current();
    $pageType = $seoData['type'] ?? 'website';
    $isErrorPage = isset($exception) && $exception instanceof \Throwable;
    $pageRobots = $isErrorPage ? 'noindex,nofollow' : ($seoData['robots'] ?? 'index,follow');
    $currentLocale = app()->getLocale();
    $availableLocales = config('locales.available', ['es', 'en']);
    $twitterHandle = setting('twitter_url') ? '@'.basename(rtrim((string) setting('twitter_url'), '/')) : null;
@endphp
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta name="theme-color" content="#020617" />
<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDescription }}" />
<meta name="author" content="{{ $seoData['author'] ?? setting('site_title', config('app.name')) }}" />
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
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
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
