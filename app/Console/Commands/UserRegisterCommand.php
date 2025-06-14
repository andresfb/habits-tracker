<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use RuntimeException;
use Throwable;
use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class UserRegisterCommand extends Command
{
    protected $signature = 'user:register';

    protected $description = 'Register a new user.';

    public function handle(): void
    {
        try {
            clear();
            intro('Register a new user');

            $fullName = text(
                label: 'Full Name:',
                placeholder: 'John Doe',
                required: true,
                validate: 'string|max:255'
            );

            $email = text(
                label: 'Email:',
                placeholder: 'user@example.com',
                required: true,
                validate: 'string|lowercase|email|max:255',
            );

            if (User::where('email', $email)->exists()) {
                throw new RuntimeException('Email already exists.');
            }

            $password = password(
                label: 'Password:',
                required: true,
                validate:  Password::defaults(),
                hint: 'Minimum 8 characters.'
            );

            $confirmPassword = password(
                label: 'Confirm Password:',
                required: true,
            );

            if ($password !== $confirmPassword) {
                throw new RuntimeException('Passwords do not match.');
            }

            $user = User::create([
                'name' => $fullName,
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);

            if ($user === null) {
                throw new RuntimeException('User could not be created.');
            }

            outro('User registered successfully.');
        } catch (Throwable $e) {
            error("\nSomething went wrong:\n" . $e->getMessage());
        } finally {
            $this->line('');
        }
    }
}
