<?php

declare(strict_types=1);

namespace App\Console\Tasks\Habits\AddHabit;

use App\Console\Dtos\TaskResultItem;
use App\Console\Interfaces\TaskInterface;
use App\Console\Services\AuthService;
use App\Console\Tasks\Habits\ListHabits\ListHabitsTask;
use App\Models\Category;
use App\Models\Habit;
use App\Models\Period;
use App\Models\Unit;
use App\Services\CategoriesService;
use App\Services\PeriodsService;
use App\Services\UnitsService;
use Illuminate\Contracts\Auth\Authenticatable;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\form;
use function Laravel\Prompts\info;

final readonly class AddHabitTask implements TaskInterface
{
    public function __construct(
        private ListHabitsTask $listHabitsTask,
        private CategoriesService $categoriesService,
        private UnitsService $unitsService,
        private PeriodsService $periodsService,
    ) {}

    public function handle(): TaskResultItem
    {
        $user = AuthService::user();

        $this->listHabitsTask->display(
            $this->listHabitsTask->getHabits($user)
        );

        info('Create a Habit');

        $entry = $this->getEntry($user);
        $entry['user_id'] = $user->getAuthIdentifier();

        $habit = Habit::create($entry);
        if ($habit === null) {
            return new TaskResultItem(false, 'Could not create category.');
        }

        if (confirm('Do you want to add another habit?')) {
            return $this->handle();
        }

        return new TaskResultItem(
            success: true,
            message: 'Habit created successfully.'
        );
    }

    private function getEntry(Authenticatable $user): array
    {
        $nextOrder = Habit::query()
            ->where('user_id', $user->getAuthIdentifier())
            ->max('order_by');

        return form()
            ->text(
                label: 'Name:',
                required: true,
                validate: 'string|max:100',
                name: 'name',
            )
            ->textarea(
                label: 'Description:',
                required: true,
                rows: 3,
                name: 'description',
            )
            ->select(
                label: 'Select a Category:',
                options: $this->categoriesService->getSelectableList(),
                scroll: 10,
                name: 'category_id',
            )
            ->text(
                label: 'Target Value:',
                default: '1',
                required: true,
                validate: 'numeric',
                name: 'target_value',
            )
            ->text(
                label: 'Default Value:',
                default: '1',
                required: true,
                validate: 'numeric',
                name: 'default_value',
            )
            ->select(
                label: 'Select a Unit:',
                options: $this->unitsService->getSelectableList(),
                scroll: 10,
                name: 'unit_id',
            )
            ->select(
                label: 'Select a period:',
                options: $this->periodsService->getSelectableList(),
                name: 'period_id',
            )
            ->confirm(
                label: 'Allow Multiple Times per Period?',
                default: false,
                name: 'allow_multiple_times',
            )
            ->textarea(
                label: 'Notes:',
                rows: 3,
                name: 'notes',
            )
            ->text(
                label: 'Order:',
                default: (string) ($nextOrder + 1),
                required: true,
                validate: 'integer',
                name: 'order_by',
            )
            ->submit();
    }
}
