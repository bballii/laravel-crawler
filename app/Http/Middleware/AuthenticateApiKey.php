<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key') ?? $request->query('api_key');

        if (!$apiKey) {
            return response()->json(['message' => 'API key is required'], 401);
        }

        // Hash the provided key to compare with stored hash
        $hashedKey = hash('sha256', $apiKey);
        $key = ApiKey::where('key', $hashedKey)->first();

        if (!$key || $key->isExpired()) {
            return response()->json(['message' => 'Invalid or expired API key'], 401);
        }

        // Update last used timestamp
        $key->updateLastUsed();

        // Set the authenticated user
        auth()->setUser($key->user);

        return $next($request);
    }
}

