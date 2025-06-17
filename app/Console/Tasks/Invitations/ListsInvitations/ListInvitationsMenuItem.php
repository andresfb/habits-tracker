<?php

namespace App\Console\Tasks\Invitations\ListsInvitations;

use App\Console\Interfaces\MenuItemInterface;
use App\Console\Interfaces\TaskInterface;

class ListInvitationsMenuItem implements MenuItemInterface
{
    public function itemName(): string
    {
        return 'List Invitations';
    }

    public function task(): TaskInterface
    {
        return app(ListInvitationsTask::class);
    }
}
