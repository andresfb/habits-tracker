<?php

namespace App\Listeners;

use App\Enums\InvitationStatus;
use App\Events\InvitationRejectedEvent;
use App\Mail\InvitationRejectedMail;
use App\Models\Invitation;
use Illuminate\Support\Facades\Mail;

class InvitationRejectedListener
{
    public function handle(InvitationRejectedEvent $event): void
    {
        $invitation = Invitation::where('id', $event->invitationId)
            ->where('status', InvitationStatus::REJECTED)
            ->firstOrFail();

        Mail::to($invitation->email)
            ->send(new InvitationRejectedMail($event->reason));
    }
}
