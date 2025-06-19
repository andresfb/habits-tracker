<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\InvitationStatus;
use App\Events\InvitationApprovedEvent;
use App\Mail\InvitationApprovedMail;
use App\Models\Invitation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

final class InvitationApprovedListener implements ShouldQueue
{
    public function handle(InvitationApprovedEvent $event): void
    {
        $invitation = Invitation::where('id', $event->invitationId)
            ->where('status', InvitationStatus::APPROVED)
            ->firstOrFail();

        Mail::to($invitation->email)
            ->send(new InvitationApprovedMail($invitation->id));
    }
}
