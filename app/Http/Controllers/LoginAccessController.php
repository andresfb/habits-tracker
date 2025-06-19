<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class LoginAccessController extends Controller
{
    public function __invoke(Request $request, string $email): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        $user = User::query()->where('email', $email)
            ->firstOrFail();

        Auth::login($user);

        request()->session()->regenerate();

        return new RedirectResponse(
            url: route('home'),
        );
    }
}
