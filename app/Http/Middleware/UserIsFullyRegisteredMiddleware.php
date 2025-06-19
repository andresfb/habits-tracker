<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

final class UserIsFullyRegisteredMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->user()->isRegistered()) {
            if ($request->expectsJson()) {
                abort(403, 'User is not fully registered');
            } else {
                Redirect::route('invitation');
            }
        }

        return $next($request);
    }
}
