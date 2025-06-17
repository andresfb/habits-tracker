<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

final readonly class RegisterUserAction
{
    /**
     * @throws Throwable
     */
    public function handle(string $token): void
    {
        DB::transaction(static function () use ($token): void {
            $invitation = Invitation::where('token', $token)
                ->where('status', InvitationStatus::APPROVED)
                ->firstOrFail();

            $user = User::create([
                'name' => $invitation->name,
                'email' => $invitation->email,
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]);

            if ($user === null) {
                throw new RuntimeException('User could not be created');
            }

            $invitation->complete(
                $user->id,
                $token
            );

            auth()->login($user);

            request()->session()->regenerate();
        });
    }
}
