<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class InvitationRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private readonly Invitation $invitation;

    public function __construct(private readonly int $invitationId)
    {
        $this->invitation = Invitation::findOrFail($this->invitationId);
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => 'A new invitation has been requested',
            'request_email' => $this->invitation->email,
        ];
    }
}
