<?php

declare(strict_types=1);

namespace Codenaline\LaravelIdempotency\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PurgeExpiredIdempotencyKeys extends Command
{
    protected $signature = 'idempotency:purge';

    protected $description = 'Remove expired idempotency keys from the database';

    public function handle(): int
    {
        $connection = config('idempotency.drivers.database.connection', config('database.default'));
        $table = config('idempotency.drivers.database.table', 'idempotency_keys');

        $count = DB::connection($connection)
            ->table($table)
            ->where('expires_at', '<', now())
            ->delete();

        $this->info("Purged {$count} expired idempotency keys.");

        return self::SUCCESS;
    }
}
