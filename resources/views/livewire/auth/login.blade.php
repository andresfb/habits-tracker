<?php

use App\Actions\SendLoginLinkAction;
use Illuminate\Http\RedirectResponse;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new
#[Layout('components.layouts.empty')]
#[Title('Login')]
class extends Component {

    #[Rule('required|email')]
    public string $email = '';

    public bool $showForm = true;

    public function mount(): ?RedirectResponse
    {
        // It is logged in
        if (auth()->user()) {
            return redirect()->route('home');
        }

        return null;
    }

    public function login(SendLoginLinkAction $action): void
    {
        $this->validate();

        $action->handle(
            email: $this->email,
        );

        $this->showForm = false;
    }
}; ?>

<div class="md:w-96 mx-auto mt-20">
    <div class="mb-10">
        <x-app-brand/>
    </div>

    @if ($showForm)
        <x-form wire:submit="login">
            @csrf

            <div class="text-2xl font-semibold mb-2">Login</div>
            <x-input placeholder="E-mail" wire:model="email" icon="o-envelope"/>

            <x-slot:actions>
                @if (config('constants.registration_enabled'))
                    <x-button label="Create an account" class="btn-ghost" link="{{ route('invitation') }}"/>
                @endif
                <x-button label="Login" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="login"/>
            </x-slot:actions>
        </x-form>
    @else
        <div class="text-2xl text-center font-semibold mb-2">
            An email has been sent for you to log in.
        </div>
    @endif
</div>
