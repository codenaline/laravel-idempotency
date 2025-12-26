<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Idempotency Header
    |--------------------------------------------------------------------------
    |
    | The HTTP header name used to identify idempotency keys.
    | Default: "Idempotency-Key" (widely adopted convention in APIs like Stripe).
    | You can override this in your .env file:
    | IDEMPOTENCY_HEADER=X-Idempotency-Key
    |
    */

    'header' => env('IDEMPOTENCY_HEADER', 'Idempotency-Key'),

    /*
    |--------------------------------------------------------------------------
    | Default Driver
    |--------------------------------------------------------------------------
    |
    | Define the default driver used for storing idempotency keys.
    | Supported drivers: redis, database, cache, memory.
    |
    */

    'default' => env('IDEMPOTENCY_DRIVER', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Drivers Configuration
    |--------------------------------------------------------------------------
    |
    | Each driver has its own configuration options.
    | TTL values are always expressed in SECONDS.
    |
    */

    'drivers' => [

        'redis' => [
            'connection' => env('IDEMPOTENCY_REDIS_CONNECTION', 'default'),
            'ttl' => env('IDEMPOTENCY_TTL', 60),
        ],

        'database' => [
            'connection' => env('IDEMPOTENCY_DB_CONNECTION', config('database.default')),
            'table' => env('IDEMPOTENCY_DB_TABLE', 'idempotency_keys'),
            'ttl' => env('IDEMPOTENCY_TTL', 60),
        ],

        'cache' => [
            'store' => env('IDEMPOTENCY_CACHE_STORE'),
            'ttl' => env('IDEMPOTENCY_TTL', 60),
        ],
    ],
];
