<?php

declare(strict_types=1);

namespace Codenaline\LaravelIdempotency\Tests;

use Codenaline\LaravelIdempotency\LaravelIdempotencyServiceProvider;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelIdempotencyServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        config()->set('database.redis', [
            'client' => 'predis',
            'default' => [
                'host' => '127.0.0.1',
                'password' => null,
                'port' => 6379,
                'database' => 0,
            ],
        ]);

        config()->set('idempotency', require __DIR__.'/../config/idempotency.php');

        foreach (File::allFiles(__DIR__.'/../database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
        }
    }
}
