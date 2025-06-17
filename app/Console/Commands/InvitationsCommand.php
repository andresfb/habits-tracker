<?php

namespace App\Console\Commands;

class InvitationsCommand extends MenuBasedCommand
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
