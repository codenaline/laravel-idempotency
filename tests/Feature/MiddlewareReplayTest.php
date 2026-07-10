<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Config::set('idempotency.default', 'cache');
    Config::set('idempotency.drivers.cache.store', null);

    Route::post('/idempotent-response', fn () => response()
        ->json(['transaction_id' => uniqid()], 201)
        ->header('X-Request-Result', 'created')
    )->middleware('idempotency');
});

it('replays the original response body status and headers', function () {
    $firstResponse = $this->postJson('/idempotent-response', [], [
        'Idempotency-Key' => 'response-key',
    ]);
    $secondResponse = $this->postJson('/idempotent-response', [], [
        'Idempotency-Key' => 'response-key',
    ]);

    expect($secondResponse->status())->toBe(201);
    expect($secondResponse->getContent())->toBe($firstResponse->getContent());
    expect($secondResponse->headers->get('X-Request-Result'))->toBe('created');
});
