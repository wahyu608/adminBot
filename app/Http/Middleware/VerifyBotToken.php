<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class VerifyBotToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');
        $validToken = 'Bearer ' . env('BOT_API_KEY');

        if ($authHeader !== $validToken) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return $next($request);
    }
}
