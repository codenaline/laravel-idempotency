<?php

declare(strict_types=1);

namespace Codenaline\LaravelIdempotency\Middleware;

use Closure;
use Codenaline\LaravelIdempotency\Facades\LaravelIdempotency;

class EnsureIdempotency
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
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

        if (LaravelIdempotency::exists($key)) {
            return response()->json(LaravelIdempotency::get($key));
        }

        $response = $next($request);

        LaravelIdempotency::store($key, $response->getContent(), $ttl);

        return $response;
    }

}
