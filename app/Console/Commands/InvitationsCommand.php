<?php

declare(strict_types=1);

namespace App\Console\Commands;

final class InvitationsCommand extends MenuBasedCommand
{
    protected $signature = 'invitations';

    protected $description = 'Manage the Invitations';

    public function getTitle(): string
    {
        return 'Invitations';
    }

    public function getTaskKey(): string
    {
        return 'invite-tasks';
    }
}
