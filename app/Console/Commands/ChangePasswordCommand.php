<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Throwable;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\form;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;

class ChangePasswordCommand extends Command
{
    protected $signature = 'change:password';

    protected $description = 'Change the password of a given user';

    public function handle(): void
    {
        try {
            clear();
            intro('Change Password');

            $responses = form()
                ->text(
                    label: 'User email:',
                    required: true,
                    validate: 'string|email|exists:users,email',
                    name: 'email',
                )
                ->password(
                    label: 'Password:',
                    required: true,
                    validate: ['password' => 'min:8'],
                    hint: 'Minimum 8 characters.',
                    name: 'password',
                )
                ->password(
                    label: 'Confirm Password:',
                    required: true,
                    name: 'confirm_password',
                )
                ->submit();

            if ($responses['password'] !== $responses['confirm_password']) {
                throw new RuntimeException('Passwords do not match.');
            }

            User::where('email', $responses['email'])
                ->update([
                    'password' => Hash::make($responses['password']),
                ]);

            outro('Password changed successfully');
        } catch (Throwable $throwable) {
            error("\nSomething went wrong:\n".$throwable->getMessage());
        } finally {
            $this->line('');
        }
    }
}
