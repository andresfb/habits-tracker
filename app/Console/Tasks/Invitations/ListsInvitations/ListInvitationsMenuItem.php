<?php

declare(strict_types=1);

namespace App\Console\Tasks\Invitations\ListsInvitations;

use App\Console\Interfaces\MenuItemInterface;
use App\Console\Interfaces\TaskInterface;

final class ListInvitationsMenuItem implements MenuItemInterface
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
