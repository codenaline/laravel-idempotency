<?php

declare(strict_types=1);

namespace Codenaline\LaravelIdempotency\Drivers;

use Codenaline\LaravelIdempotency\Contracts\IdempotencyDriver;
use Illuminate\Support\Facades\DB;

class DatabaseDriver implements IdempotencyDriver
{
    protected string $connection;

    protected string $table;

    protected int $ttl;

    public function __construct(array $config)
    {
        $this->connection = $config['connection'];
        $this->table = $config['table'];
        $this->ttl = $config['ttl'] ?? 60;
    }

    public function has(string $key): bool
    {
        return DB::connection($this->connection)
            ->table($this->table)
            ->where('key', $key)
            ->where('expires_at', '>', now())
            ->exists();
    }

    public function put(string $key, mixed $value, ?int $ttl = null): void
    {
        $ttl = $ttl ?? $this->ttl;

        DB::connection($this->connection)
            ->table($this->table)
            ->updateOrInsert(
                ['key' => $key],
                [
                    'value' => serialize($value),
                    'expires_at' => now()->addSeconds($ttl),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
    }

    public function get(string $key): mixed
    {
        $record = DB::connection($this->connection)
            ->table($this->table)
            ->where('key', $key)
            ->where('expires_at', '>', now())
            ->first();

        return $record ? unserialize($record->value) : null;
    }

    public function forget(string $key): void
    {
        DB::connection($this->connection)
            ->table($this->table)
            ->where('key', $key)
            ->delete();
    }
}
