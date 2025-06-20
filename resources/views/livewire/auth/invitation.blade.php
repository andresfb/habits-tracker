<?php

use App\Models\Invitation;
use App\Notifications\InvitationRequestNotification;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Livewire\Volt\Component;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config:

new
#[Layout('components.layouts.empty')]
#[Title('Invitation')]
class extends Component {
    #[Rule('required')]
    public string $name = '';

    #[Rule('required|email|unique:invitations')]
    public string $email = '';

    public function mount(): ?RedirectResponse
    {
        if (! Config::boolean('constants.registration_enabled')) {
            return redirect()->route('login');
        }

        // It is logged in
        if (auth()->user()) {
            return redirect()->route('home');
        }

        return null;
    }

    public function invite(): Redirector
    {
        $data = $this->validate();
        $data['token'] = Invitation::generateToken();

        $invitation = Invitation::create($data);
        if ($invitation === null) {
            abort(503, 'Cannot accept invitations at this time');
        }

        $admin = User::getAdmin();
        if (!$admin instanceof User) {
            abort(410, 'Administrator not available');
        }

        $admin->notify(new InvitationRequestNotification(
            $invitation->id
        ));

        return redirect('/auth/sent')
            ->route('invite.sent');
    }
}; ?>

<div class="md:w-96 mx-auto mt-15">
    <div class="mb-10">
        <x-app-brand/>
    </div>

    <div class="text-center text-xl mb-10">
        Access to this system is by invitation only.
    </div>

    <div class="text-center text-lg mb-10">
        Request an invitation below.
    </div>

    <x-form wire:submit="invite">
        <x-input placeholder="Name" wire:model="name" icon="o-user"/>
        <x-input placeholder="E-mail" wire:model="email" icon="o-envelope"/>

        <x-slot:actions>
            <x-button label="Already registered?" class="btn-ghost" link="{{ route('login') }}"/>
            <x-button label="Request" type="submit" icon="o-paper-airplane" class="btn-primary" spinner="register"/>
        </x-slot:actions>
    </x-form>
</div>
