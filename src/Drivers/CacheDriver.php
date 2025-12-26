<?php

declare(strict_types=1);

namespace Codenaline\LaravelIdempotency\Drivers;

use Codenaline\LaravelIdempotency\Contracts\IdempotencyDriver;
use Illuminate\Support\Facades\Cache;

class CacheDriver implements IdempotencyDriver
{
    protected ?string $store;

    protected int $ttl;

    public function __construct(array $config)
    {
        $this->store = $config['store'] ?? null;
        $this->ttl = $config['ttl'] ?? 60;
    }

    public function has(string $key): bool
    {
        return Cache::store($this->store)->has($key);
    }

    public function put(string $key, mixed $value, ?int $ttl = null): void
    {
        $ttl = $ttl ?? $this->ttl;

        Cache::store($this->store)->put($key, $value, now()->addSeconds($ttl));
    }

    public function get(string $key): mixed
    {
        return Cache::store($this->store)->get($key);
    }

    public function forget(string $key): void
    {
        Cache::store($this->store)->forget($key);
    }
}
