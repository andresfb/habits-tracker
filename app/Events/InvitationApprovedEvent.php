<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class InvitationApprovedEvent
{
    use Dispatchable;

    public function __construct(public readonly int $invitationId) {}
}
