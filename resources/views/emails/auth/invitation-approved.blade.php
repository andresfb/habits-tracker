<x-mail::message>
    # Invitation to join {{ config('app.name') }} is Approved

    Use the link below to register.

    <x-mail::button :url="$url">
        Register
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
