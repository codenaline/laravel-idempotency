<?php

declare(strict_types=1);

use Codenaline\LaravelIdempotency\Drivers\RedisDriver;

it('stores and retrieves values in redis', function () {
    $driver = new RedisDriver(['connection' => 'default', 'ttl' => 60]);

    $driver->put('foo', 'bar');
    expect($driver->has('foo'))->toBeTrue();
    expect($driver->get('foo'))->toEqual('bar');

    $driver->forget('foo');
    expect($driver->has('foo'))->toBeFalse();
});
