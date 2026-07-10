<?php

declare(strict_types=1);

use Codenaline\LaravelIdempotency\LaravelIdempotencyServiceProvider;
use Spatie\LaravelPackageTools\Package;

it('registers migration files that exist in the package', function () {
    $package = new Package;
    $provider = new LaravelIdempotencyServiceProvider($this->app);

    $provider->configurePackage($package);

    foreach ($package->migrationFileNames as $migrationFileName) {
        $migrationPath = __DIR__."/../../database/migrations/{$migrationFileName}.php";

        expect(
            file_exists($migrationPath) || file_exists("{$migrationPath}.stub")
        )->toBeTrue();
    }
});
