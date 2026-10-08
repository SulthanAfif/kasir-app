<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->header('X-Api-Token');
        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = User::where('remember_token', hash('sha256', $token))->first();
        if (!$user) {
            return response()->json(['message' => 'Invalid token.'], 401);
        }

        $request->attributes->set('api_user', $user);
        return $next($request);
    }
}
