<?php

namespace App\Console\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Cache;
use function Laravel\Prompts\error;
use function Laravel\Prompts\form;
use function Laravel\Prompts\intro;

class AuthService
{
    private static string $cacheKey = 'HABIT:TRACKER:ACCESS:TOKEN:';

    public static function checkAccess(): bool
    {
        if (auth()->check()) {
            return true;
        }

        return self::getCacheUser() !== null;
    }

    public static function user(): Authenticatable
    {
        if (self::checkAccess()) {
            $user = self::getUser();
        }

        return $user ?? self::login();
    }

    public static function logout(): void
    {
        auth()->logout();
        Cache::forget(self::getCacheKey());
    }

    public static function login(): ?Authenticatable
    {
        intro('Login');

        $credentials = form()
            ->text(
                label: 'Email:',
                placeholder: 'user@example.com',
                required: true,
                validate: 'string|lowercase|email',
                name: 'email',
            )
            ->password(
                label: 'Password:',
                required: true,
                name: 'password',
            )
            ->submit();

        if (! auth()->attempt($credentials)) {
            error('Invalid credentials.');

            return null;
        }

        info('Logged in successfully. Access granted for 2 hours.');

        $user = auth()->user();
        Cache::put(self::getCacheKey(), $user, now()->addHours(2));

        return $user;
    }

    private static function getUser(): ?Authenticatable
    {
        return self::getCacheUser() ?? auth()?->user();
    }

    private static function getCacheUser(): ?Authenticatable
    {
        $key = self::getCacheKey();

        return Cache::get($key);
    }

    private static function getCacheKey(): string
    {
        return md5(self::$cacheKey . gethostname());
    }
}
