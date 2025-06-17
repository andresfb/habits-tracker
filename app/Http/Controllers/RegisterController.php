<?php

namespace App\Http\Controllers;

use App\Actions\RegisterUserAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class RegisterController extends Controller
{
    /**
     * @throws Throwable
     */
    public function __invoke(Request $request, RegisterUserAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'token' => 'required|string|alpha_num|size:40'
        ]);

        $action->handle(
            $validated['token']
        );

        return redirect()
            ->route('home')
            ->with('success', 'You have been registered successfully.');
    }
}
