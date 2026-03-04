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
    if ($request->bearerToken() !== config('app.bot_api_key')) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    return $next($request);
}
}
