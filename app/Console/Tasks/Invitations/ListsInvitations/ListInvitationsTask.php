<?php

namespace App\Console\Tasks\Invitations\ListsInvitations;

use App\Console\Dtos\TaskResultItem;
use App\Console\Interfaces\TaskInterface;
use App\Console\Services\AuthService;
use App\Enums\InvitationStatus;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

use function Laravel\Prompts\pause;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

class ListInvitationsTask implements TaskInterface
{
    public function handle(): TaskResultItem
    {
        /** @var User $user */
        $user = AuthService::user();
        if (! $user->isAdmin()) {
            return new TaskResultItem(
                success: false,
                message: 'You are not authorized to perform this action.'
            );
        }

        $invites = $this->getInvitations();

        if ($invites->isEmpty()) {
            return new TaskResultItem(
                success: false,
                message: 'No invitations found.'
            );
        }

        $this->display($invites);

        pause('Press ENTER to continue.');

        return new TaskResultItem(
            success: true,
            message: sprintf('Found %d invitations', $invites->count()),
        );

    }

    public function getInvitations(): Collection
    {
        return Invitation::query()
            ->where('status', InvitationStatus::CREATED)
            ->latest()
            ->get();
    }

    public function display(Collection $invites): void
    {
        if ($invites->isEmpty()) {
            warning('No invitations found.');

            return;
        }

        $list = $invites->map(fn (Invitation $invite): array => [
            $invite->id,
            $invite->name,
            $invite->email,
            $invite->created_at->format('Y-m-d H:i:s'),
        ]);

        $headers = ['Id', 'Name', 'Email', 'Created At'];

        table($headers, $list);
    }
}
