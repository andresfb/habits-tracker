<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class InvitationRejectedEvent
{
    use Dispatchable;

    public function __construct(
        public readonly int $invitationId,
        public readonly string $reason,
    ) {}
}
