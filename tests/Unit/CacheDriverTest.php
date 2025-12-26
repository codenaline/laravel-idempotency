<?php

declare(strict_types=1);

use Codenaline\LaravelIdempotency\Drivers\CacheDriver;

it('stores and retrieves values in cache', function () {
    $driver = new CacheDriver(['store' => null, 'ttl' => 60]);

    $driver->put('foo', 'bar');
    expect($driver->has('foo'))->toBeTrue();
    expect($driver->get('foo'))->toEqual('bar');

    $driver->forget('foo');
    expect($driver->has('foo'))->toBeFalse();
});
