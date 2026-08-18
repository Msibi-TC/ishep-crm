<?php

return [
    'env' => env('APP_ENV', 'production'),
    'debug' => filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN),
    'url' => rtrim(env('APP_URL', 'https://preview.example.com'), '/'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'key' => env('APP_KEY', ''),
    'session' => ['name' => env('SESSION_NAME', 'ishep_plain_session'), 'lifetime' => (int) env('SESSION_LIFETIME', '120'), 'secure' => filter_var(env('SESSION_SECURE', 'false'), FILTER_VALIDATE_BOOLEAN), 'samesite' => env('SESSION_SAMESITE', 'Lax')],
    'mail' => ['driver' => env('MAIL_DRIVER', 'log'), 'from_address' => env('MAIL_FROM_ADDRESS', 'no-reply@example.com'), 'from_name' => env('MAIL_FROM_NAME', 'ISHEP CRM')],
];
