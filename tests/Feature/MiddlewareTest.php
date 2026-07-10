<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

dataset('drivers', [
    'database',
    'cache',
    'redis',
]);

beforeEach(function () {
    Route::post('/payments', fn () => response()->json([
        'transaction_id' => uniqid(),
    ]))->middleware('idempotency');
});

it('works with different drivers', function ($driver) {
    Config::set('idempotency.default', $driver);

    if ($driver === 'redis') {
        $connection = @fsockopen('127.0.0.1', 6379);

        if (! $connection) {
            $this->markTestSkipped('Redis is not available on 127.0.0.1:6379.');
        }

        fclose($connection);
    }

    $firstResponse = $this->postJson('/payments', [], ['Idempotency-Key' => 'abc123']);
    $secondResponse = $this->postJson('/payments', [], ['Idempotency-Key' => 'abc123']);

    $firstResponse->assertOk();
    $secondResponse->assertOk();

    expect($firstResponse->json('transaction_id'))->not->toBeNull();
    expect($firstResponse->json('transaction_id'))->toEqual($secondResponse->json('transaction_id'));
})->with('drivers');
