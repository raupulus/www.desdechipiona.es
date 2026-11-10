<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
        'min_score' => (float) env('RECAPTCHA_MIN_SCORE', 0.5),
    ],

    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'raupulus_weather' => [
        'url' => env('RAUPULUS_WEATHER_API_URL', ''),
        'token' => env('RAUPULUS_WEATHER_API_TOKEN', env('AEMET_API_KEY')),
        'timeout' => (int) env('RAUPULUS_WEATHER_TIMEOUT', 4),
    ],

    'chipiona' => [
        'corpus_path' => env('CHIPIONA_CORPUS_PATH'),
    ],

    'adsense' => [
        'enabled' => (bool) env('ADSENSE_ENABLED', false),
        'preview' => (bool) env('ADSENSE_PREVIEW', env('APP_ENV') === 'local'),
        'client_id' => env('ADSENSE_CLIENT_ID', ''),
        'slot_sidebar' => env('ADSENSE_SLOT_SIDEBAR', ''),
        'slot_horizontal' => env('ADSENSE_SLOT_HORIZONTAL', ''),
    ],

];
