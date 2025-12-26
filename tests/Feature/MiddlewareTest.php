<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

dataset('drivers', [
    'redis',
    'database',
    'cache',
]);

beforeEach(function () {
    Route::post('/payment', fn () => response()->json([
        'transaction_id' => uniqid(),
    ]))->middleware('idempotency');
});

it('works with different drivers', function ($driver) {
    Config::set('idempotency.default', $driver);

    $firstResponse = $this->postJson('/payments', [], ['Idempotency-Key' => 'abc123']);
    $secondResponse = $this->postJson('/payments', [], ['Idempotency-Key' => 'abc123']);

    expect($firstResponse->json('transaction_id'))->toEqual($secondResponse->json('transaction_id'));
})->with('drivers');
