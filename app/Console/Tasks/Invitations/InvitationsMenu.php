<?php

namespace App\Console\Tasks\Invitations;

use App\Console\Interfaces\MenuInterface;
use App\Console\Tasks\Invitations\ApproveInvitation\ApproveInvitationMenuItem;
use App\Console\Tasks\Invitations\ListsInvitations\ListInvitationsMenuItem;
use Illuminate\Support\Collection;

class InvitationsMenu implements MenuInterface
{
    /**
     * @inheritDoc
     */
    public function getMenuItems(): Collection
    {
        return collect([
            app(ListInvitationsMenuItem::class),
            app(ApproveInvitationMenuItem::class),
        ]);
    }
}
