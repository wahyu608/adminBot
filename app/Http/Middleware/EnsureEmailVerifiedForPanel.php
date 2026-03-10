<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerifiedForPanel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if ($user && ! $user->hasVerifiedEmail()) {

            if ($request->routeIs('filament.admin.pages.verify-email-notice')) {
                return $next($request);
            }

            return redirect()->route('filament.admin.pages.verify-email-notice');
        }

        return $next($request);
    }
}
