<?php

declare(strict_types=1);

namespace App\Actions;

use App\Mail\LoginLinkMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

final readonly class SendLoginLinkAction
{
    public function handle(string $email): void
    {
        $user = User::where('email', $email)
            ->first();

        if (! $user) {
            Log::warning("Login attempt with invalid email: {$email}");

            return;
        }

        if (! $user->hasVerifiedEmail()) {
            Log::warning("Login attempt with unverified email: {$email}");

            return;
        }

        if (! $user->isRegistered()) {
            Log::warning("Login attempt with unregistered email: {$email}");

            return;
        }

        Mail::to(
            users: $email,
        )->send(
            mailable: new LoginLinkMail(
                url: URL::temporarySignedRoute(
                    name: 'login.auth',
                    expiration: 3600,
                    parameters: [
                        'email' => $email,
                    ],
                ),
            )
        );
    }
}
