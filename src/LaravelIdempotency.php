<?php

declare(strict_types=1);

namespace Codenaline\LaravelIdempotency;

use Codenaline\LaravelIdempotency\Contracts\IdempotencyDriver;
use Codenaline\LaravelIdempotency\Drivers\CacheDriver;
use Codenaline\LaravelIdempotency\Drivers\DatabaseDriver;
use Codenaline\LaravelIdempotency\Drivers\RedisDriver;
use InvalidArgumentException;

class LaravelIdempotency
{
    public function exists(string $key): bool
    {
        return $this->driver()->has($key);
    }

    public function store(string $key, mixed $value, ?int $ttl = null): void
    {
        $this->driver()->put($key, $value, $ttl);
    }

    public function get(string $key): mixed
    {
        return $this->driver()->get($key);
    }

    public function forget(string $key): void
    {
        $this->driver()->forget($key);
    }

    protected function driver(): IdempotencyDriver
    {
        $driver = config('idempotency.default', 'redis');
        $config = config("idempotency.drivers.{$driver}", []);

        return match ($driver) {
            'cache' => new CacheDriver($config),
            'database' => new DatabaseDriver($config),
            'redis' => new RedisDriver($config),
            default => throw new InvalidArgumentException("Unsupported idempotency driver [{$driver}]."),
        };
    }
}
