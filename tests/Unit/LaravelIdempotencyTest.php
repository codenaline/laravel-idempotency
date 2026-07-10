<?php

declare(strict_types=1);

use Codenaline\LaravelIdempotency\Facades\LaravelIdempotency;
use Illuminate\Support\Facades\Config;

it('stores and retrieves values through the configured driver', function () {
    Config::set('idempotency.default', 'cache');
    Config::set('idempotency.drivers.cache.store', null);

    LaravelIdempotency::store('request-key', 'response-body');

    expect(LaravelIdempotency::exists('request-key'))->toBeTrue();
    expect(LaravelIdempotency::get('request-key'))->toBe('response-body');

    LaravelIdempotency::forget('request-key');
    expect(LaravelIdempotency::exists('request-key'))->toBeFalse();
});
