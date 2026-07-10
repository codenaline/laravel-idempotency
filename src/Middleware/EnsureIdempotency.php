<?php

declare(strict_types=1);

namespace Codenaline\LaravelIdempotency\Middleware;

use Closure;
use Codenaline\LaravelIdempotency\Facades\LaravelIdempotency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EnsureIdempotency
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next, ?int $ttl = null)
    {
        $key = $request->header(config('idempotency.header'));

        if (! $key) {
            return response()->json([
                'error' => 'Idempotency-Key header is required',
            ], 400);
        }

        return Cache::store(config('idempotency.lock.store'))
            ->lock($this->lockKey($key), config('idempotency.lock.seconds', 10))
            ->block(config('idempotency.lock.wait_seconds', 10), function () use ($key, $next, $request, $ttl) {
                if (LaravelIdempotency::exists($key)) {
                    return $this->storedResponse(LaravelIdempotency::get($key));
                }

                $response = $next($request);

                LaravelIdempotency::store($key, [
                    'content' => $response->getContent(),
                    'status' => $response->getStatusCode(),
                    'headers' => $response->headers->all(),
                ], $ttl);

                return $response;
            });
    }

    protected function storedResponse(array $storedResponse)
    {
        return response(
            $storedResponse['content'],
            $storedResponse['status'],
            $storedResponse['headers']
        );
    }

    protected function lockKey(string $key): string
    {
        return "idempotency:{$key}";
    }
}
