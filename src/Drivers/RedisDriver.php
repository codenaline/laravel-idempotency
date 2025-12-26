<?php

declare(strict_types=1);

namespace Codenaline\LaravelIdempotency\Drivers;

use Codenaline\LaravelIdempotency\Contracts\IdempotencyDriver;
use Illuminate\Support\Facades\Redis;

class RedisDriver implements IdempotencyDriver
{
    protected string $connection;

    protected int $ttl;

    public function __construct(array $config)
    {
        $this->connection = $config['connection'] ?? 'default';
        $this->ttl = $config['ttl'] ?? 60;
    }

    public function has(string $key): bool
    {
        return Redis::connection($this->connection)->exists($key) === 1;
    }

    public function put(string $key, mixed $value, ?int $ttl = null): void
    {
        $ttl = $ttl ?? $this->ttl;
        Redis::connection($this->connection)->setex($key, $ttl, serialize($value));
    }

    public function get(string $key): mixed
    {
        $value = Redis::connection($this->connection)->get($key);

        return $value !== false ? unserialize($value) : null;
    }

    public function forget(string $key): void
    {
        Redis::connection($this->connection)->del($key);
    }
}
