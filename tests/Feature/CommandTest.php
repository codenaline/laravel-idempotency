<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->connection = config('idempotency.drivers.database.connection', 'testing');
    $this->table = config('idempotency.drivers.database.table', 'idempotency_keys');

    DB::connection($this->connection)->table($this->table)->delete();
});

it('purges expired idempotency keys', function () {
    DB::connection($this->connection)->table($this->table)->insert([
        'key' => 'expired-key',
        'value' => serialize('foo'),
        'expires_at' => now()->subMinute(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::connection($this->connection)->table($this->table)->insert([
        'key' => 'valid-key',
        'value' => serialize('bar'),
        'expires_at' => now()->addMinute(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Artisan::call('idempotency:purge');

    $output = Artisan::output();
    expect($output)->toContain('Purged 1 expired idempotency keys.');

    $expiredExists = DB::connection($this->connection)->table($this->table)->where('key', 'expired-key')->exists();
    $validExists = DB::connection($this->connection)->table($this->table)->where('key', 'valid-key')->exists();

    expect($expiredExists)->toBeFalse();
    expect($validExists)->toBeTrue();
});
