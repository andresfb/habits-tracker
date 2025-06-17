<?php

namespace App\Http\Middleware;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use Closure;
use Illuminate\Http\Request;

class HasInvitationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        /**
         * Only for GET requests. Otherwise, this middleware will block our registration.
         */
        if (! $request->isMethod('get')) {
            return $next($request);
        }

        // validate the request
        $validated = $request->validate([
            'token' => 'bail|required|string|alpha_num|size:40'
        ]);

        $invitation = Invitation::where('token', $validated['token'])
            ->where('status', InvitationStatus::APPROVED)
            ->first();

        if (is_null($invitation)) {
            abort(404, 'The invitation does not exist.');
        }

        // check if users already registered.
        if (! is_null($invitation->registered_at)) {
            abort(403, 'The invitation has already been used.');
        }

        return $next($request);
    }
}
