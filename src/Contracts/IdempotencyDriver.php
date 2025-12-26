<?php

declare(strict_types=1);

namespace Codenaline\LaravelIdempotency\Contracts;

interface IdempotencyDriver
{
    /**
     * Determine if the given key exists in storage.
     */
    public function has(string $key): bool;

    /**
     * Persist a value for the given key with a TTL (in seconds).
     */
    public function put(string $key, mixed $value, ?int $ttl = null): void;

    /**
     * Retrieve the value for the given key.
     */
    public function get(string $key): mixed;

    /**
     * Remove the given key from storage.
     */
    public function forget(string $key): void;
}
