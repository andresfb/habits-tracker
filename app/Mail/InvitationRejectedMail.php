<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationRejectedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly string $reason
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation Rejected',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.auth.invitation-rejected',
            with: [
                'reason' => $this->reason,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
