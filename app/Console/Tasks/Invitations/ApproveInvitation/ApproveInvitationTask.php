<?php

declare(strict_types=1);

namespace App\Console\Tasks\Invitations\ApproveInvitation;

use App\Console\Dtos\TaskResultItem;
use App\Console\Interfaces\TaskInterface;
use App\Console\Services\AuthService;
use App\Console\Tasks\Invitations\ListsInvitations\ListInvitationsTask;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\select;

final readonly class ApproveInvitationTask implements TaskInterface
{
    public function __construct(private ListInvitationsTask $listTask) {}

    /**
     * @throws Throwable
     */
    public function handle(): TaskResultItem
    {
        try {
            DB::beginTransaction();

            /** @var User $user */
            $user = AuthService::user();
            if (! $user->isAdmin()) {
                return new TaskResultItem(
                    success: false,
                    message: 'You are not authorized to perform this action.'
                );
            }

            info('Approve an Invitation');

            $this->listTask->display(
                $this->listTask->getInvitations()
            );

            $selection = select(
                label: 'Select an invitation to approve:',
                options: Invitation::getPendingList(),
                scroll: 10,
            );

            $invitation = Invitation::where('id', $selection)
                ->firstOrFail();

            $approve = confirm(
                label: "Approve invitation for $invitation->name?",
            );

            if (! $approve) {
                return new TaskResultItem(
                    success: false,
                    message: 'Invitation not approved.'
                );
            }

            $invitation->approve();
            DB::commit();

            return new TaskResultItem(
                success: true,
                message: 'Invitation approved.'
            );
        } catch (Throwable $throwable) {
            DB::rollBack();

            return new TaskResultItem(
                success: false,
                message: $throwable->getMessage(),
            );
        }
    }
}
