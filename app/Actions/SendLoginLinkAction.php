<?php

declare(strict_types=1);

namespace App\Actions;

use App\Mail\LoginLinkMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

final readonly class SendLoginLinkAction
{
    public function handle(string $email): void
    {
        $key = md5("user:link:sent:$email");
        if (Cache::has($key)) {
            Log::warning("We already sent a login link to: $email");

            return;
        }

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

        $signedUrl = URL::temporarySignedRoute(
            name: 'login.auth',
            expiration: now()->addHour(),
            parameters: ['email' => $email]
        );

        Log::notice("Login link: $signedUrl");

        Mail::to(
            users: $email,
        )->send(
            mailable: new LoginLinkMail($signedUrl)
        );

        Cache::tags('users')
            ->put($key, $email, now()->addHours(2));
    }
}
