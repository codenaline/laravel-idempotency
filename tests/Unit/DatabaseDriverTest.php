<?php

declare(strict_types=1);

use Codenaline\LaravelIdempotency\Drivers\DatabaseDriver;

it('stores and retrieves values in database', function () {
    $driver = new DatabaseDriver([
        'connection' => 'testing',
        'table' => 'idempotency_keys',
        'ttl' => 60,
    ]);

    $driver->put('foo', 'bar');
    expect($driver->has('foo'))->toBeTrue();
    expect($driver->get('foo'))->toEqual('bar');

    $driver->forget('foo');
    expect($driver->has('foo'))->toBeFalse();
});
