<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimitingMiddleware
{
    protected RateLimiter $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestSignature($request);
        $maxAttempts = $this->getMaxAttempts($request);
        $decayMinutes = $this->getDecayMinutes($request);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return $this->buildResponse($key, $maxAttempts);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response, $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    protected function resolveRequestSignature(Request $request): string
    {
        $user = Auth::user();
        $identifier = $user ? $user->id : $request->ip();
        
        return sha1($identifier . '|' . $request->route()?->uri() . '|' . $request->method());
    }

    protected function getMaxAttempts(Request $request): int
    {
        // Different limits for different user types
        $user = Auth::user();
        
        if ($user && $user->hasRole('admin')) {
            return 1000; // Admin users get higher limits
        }
        
        if ($user && $user->hasRole('premium')) {
            return 500; // Premium users get higher limits
        }
        
        // Default limits based on endpoint
        $endpoint = $request->route()?->uri();
        
        if (str_contains($endpoint, 'auth')) {
            return 10; // Authentication endpoints are more restrictive
        }
        
        if (str_contains($endpoint, 'contracts/generate-pdf')) {
            return 20; // PDF generation is resource-intensive
        }
        
        return 60; // Default limit
    }

    protected function getDecayMinutes(Request $request): int
    {
        // Different decay times for different endpoints
        $endpoint = $request->route()?->uri();
        
        if (str_contains($endpoint, 'auth')) {
            return 15; // 15 minutes for auth endpoints
        }
        
        return 1; // 1 minute for most endpoints
    }

    protected function buildResponse(string $key, int $maxAttempts): Response
    {
        $retryAfter = $this->limiter->availableIn($key);
        
        return response()->json([
            'success' => false,
            'message' => 'Too many requests. Please try again later.',
            'retry_after' => $retryAfter,
            'max_attempts' => $maxAttempts,
        ], 429)->withHeaders([
            'Retry-After' => $retryAfter,
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => 0,
            'X-RateLimit-Reset' => time() + $retryAfter,
        ]);
    }

    protected function addHeaders(Response $response, int $maxAttempts, int $remainingAttempts): Response
    {
        return $response->withHeaders([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ]);
    }

    protected function calculateRemainingAttempts(string $key, int $maxAttempts): int
    {
        return $maxAttempts - $this->limiter->attempts($key) + 1;
    }
}
