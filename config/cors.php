<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'https://www.purplegridmarketing.com',
        'https://purplegridmarketing.com',
        'https://admin.purplegridmarketing.com',
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        'http://localhost:8000',
        'http://127.0.0.1:8000',
    ],
    'allowed_origins_patterns' => [
        '/^https?:\\/\\/(www\\.)?purplegridmarketing\\.com$/',
        '/^https?:\\/\\/admin\\.purplegridmarketing\\.com$/',
        '/^http:\\/\\/localhost:\\d+$/',
        '/^http:\\/\\/127\\.0\\.0\\.1:\\d+$/',
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,

];
