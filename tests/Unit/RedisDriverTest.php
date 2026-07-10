<?php

declare(strict_types=1);

use Codenaline\LaravelIdempotency\Drivers\RedisDriver;
use Illuminate\Support\Facades\Redis;

it('stores and retrieves values in redis', function () {
    $connection = new class
    {
        public array $values = [];

        public function setex(string $key, int $ttl, mixed $value): void
        {
            $this->values[$key] = $value;
        }

        public function exists(string $key): int
        {
            return array_key_exists($key, $this->values) ? 1 : 0;
        }

        public function get(string $key): mixed
        {
            return $this->values[$key] ?? false;
        }

        public function del(string $key): void
        {
            unset($this->values[$key]);
        }
    };

    Redis::shouldReceive('connection')
        ->with('default')
        ->andReturn($connection);

    $driver = new RedisDriver(['connection' => 'default', 'ttl' => 60]);

    $driver->put('foo', 'bar');
    expect($driver->has('foo'))->toBeTrue();
    expect($driver->get('foo'))->toEqual('bar');

    $driver->forget('foo');
    expect($driver->has('foo'))->toBeFalse();
});
