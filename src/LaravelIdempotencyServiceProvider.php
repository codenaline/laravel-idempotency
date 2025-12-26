<?php

declare(strict_types=1);

namespace Codenaline\LaravelIdempotency;

use Codenaline\LaravelIdempotency\Commands\LaravelIdempotencyCommand;
use Codenaline\LaravelIdempotency\Commands\PurgeExpiredIdempotencyKeys;
use Codenaline\LaravelIdempotency\Middleware\EnsureIdempotency;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelIdempotencyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-idempotency')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_idempotency_table')
            ->hasCommand(PurgeExpiredIdempotencyKeys::class);
    }

    public function bootingPackage()
    {
        $this->app['router']->aliasMiddleware('idempotency', EnsureIdempotency::class);
    }
}
