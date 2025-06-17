<?php

namespace App\Console\Tasks\Invitations\ApproveInvitation;

use App\Console\Interfaces\MenuItemInterface;
use App\Console\Interfaces\TaskInterface;

class ApproveInvitationMenuItem implements MenuItemInterface
{
    public function itemName(): string
    {
        return 'Approve Invitation';
    }

    public function task(): TaskInterface
    {
        return app(ApproveInvitationTask::class);
    }
}
