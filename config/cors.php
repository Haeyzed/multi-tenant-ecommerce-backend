<?php

$appUrl = (string) env('APP_URL', 'http://localhost');
$appHost = parse_url($appUrl, PHP_URL_HOST) ?: 'localhost';

$extraOrigins = array_values(array_filter(array_map(
    trim(...),
    explode(',', (string) env('CORS_ALLOWED_ORIGINS', ''))
)));

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Required when Scramble "Try It" runs on the central host and calls a
    | tenant subdomain (or the reverse). Herd *.test local development.
    |
    */

    'paths' => ['api/*', 'docs/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_merge(
        $extraOrigins,
        array_filter([
            rtrim($appUrl, '/'),
            str_replace('http://', 'https://', rtrim($appUrl, '/')),
        ])
    ),

    'allowed_origins_patterns' => array_values(array_filter([
        $appHost !== 'localhost'
            ? '#^https?://([a-z0-9-]+\.)?'.preg_quote($appHost, '#').'$#i'
            : null,
    ])),

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
