<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    private readonly Invitation $invitation;

    public function __construct(private readonly int $invitationId)
    {
        $this->invitation = Invitation::findOrFail($this->invitationId);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation Approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invitation-approved',
            with: [
                'url' => route('register', ['token' => $this->invitation->token]),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
