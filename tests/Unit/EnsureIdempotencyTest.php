<?php

declare(strict_types=1);

use Codenaline\LaravelIdempotency\Facades\LaravelIdempotency;
use Codenaline\LaravelIdempotency\Middleware\EnsureIdempotency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

it('checks for an existing response inside the idempotency lock before running the request', function () {
    Config::set('idempotency.header', 'Idempotency-Key');
    Config::set('idempotency.lock.store', null);
    Config::set('idempotency.lock.seconds', 10);
    Config::set('idempotency.lock.wait_seconds', 10);

    $lock = new class
    {
        public function block(int $seconds, Closure $callback): mixed
        {
            expect($seconds)->toBe(10);

            return $callback();
        }
    };

    Cache::shouldReceive('store')
        ->once()
        ->with(null)
        ->andReturnSelf();

    Cache::shouldReceive('lock')
        ->once()
        ->with('idempotency:race-key', 10)
        ->andReturn($lock);

    LaravelIdempotency::shouldReceive('exists')
        ->with('race-key')
        ->once()
        ->andReturnTrue();

    LaravelIdempotency::shouldReceive('get')
        ->with('race-key')
        ->once()
        ->andReturn([
            'content' => '{"transaction_id":"stored"}',
            'status' => 201,
            'headers' => ['x-request-result' => ['created']],
        ]);

    LaravelIdempotency::shouldReceive('store')->never();

    $requestWasHandled = false;
    $request = Request::create('/payments', 'POST', server: [
        'HTTP_IDEMPOTENCY_KEY' => 'race-key',
    ]);

    $response = (new EnsureIdempotency)->handle($request, function () use (&$requestWasHandled) {
        $requestWasHandled = true;

        return response()->json(['transaction_id' => 'fresh']);
    });

    expect($requestWasHandled)->toBeFalse();
    expect($response->getStatusCode())->toBe(201);
    expect($response->getContent())->toBe('{"transaction_id":"stored"}');
});
