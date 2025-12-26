<?php

declare(strict_types=1);

namespace Codenaline\LaravelIdempotency\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Codenaline\LaravelIdempotency\LaravelIdempotency
 */
class LaravelIdempotency extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Codenaline\LaravelIdempotency\LaravelIdempotency::class;
    }
}
