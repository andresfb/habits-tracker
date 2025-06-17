<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new
#[Layout('components.layouts.empty')]
#[Title('Invitation Request Sent')]
class extends Component {
    //
}; ?>

<div class="mt-40">
    <div class="md:w-96 mx-auto mb-20">
        <x-app-brand />
    </div>

    <div class="text-center mb-10">
        <p class="text-3xl md:text-5xl font-semibold mb-10">Your invitation has been sent.</p>
        <p class="text-2xl md:text-4xl">Please allow 48 hours for the invitation to be reviewed.</p>
    </div>

</div>
