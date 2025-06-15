<?php

declare(strict_types=1);

namespace App\Console\Tasks\Habits\ListHabits;

use App\Console\Dtos\TaskResultItem;
use App\Console\Interfaces\TaskInterface;
use App\Console\Services\AuthService;
use App\Models\Habit;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;

use function Laravel\Prompts\pause;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

final class ListHabitsTask implements TaskInterface
{
    public function handle(): TaskResultItem
    {
        $user = AuthService::user();

        $habits = $this->getHabits($user);

        if ($habits->isEmpty()) {
            return new TaskResultItem(
                success: false,
                message: 'No habits found.'
            );
        }

        $this->displayHabits($habits);

        pause('Press ENTER to continue.');

        return new TaskResultItem(
            success: true,
            message: sprintf('Found %d habits', $habits->count()),
        );
    }

    public function getHabits(Authenticatable $user): Collection
    {
        return Habit::query()
            ->with('unit', 'period')
            ->where('user_id', $user->getAuthIdentifier())
            ->orderBy('order_by')
            ->get();
    }

    public function displayHabits(Collection $habits): void
    {
        if ($habits->isEmpty()) {
            warning('No habits found.');

            return;
        }

        $list = $habits->map(fn (Habit $habit): array => [
            $habit->id,
            $habit->name,
            str($habit->description)->wordWrap()->value(),
            $habit->target_value,
            $habit->unit->name,
            $habit->period->name,
            $habit->allow_multiple_times ? 'Yes' : 'No',
            $habit->order_by,
        ]);

        $headers = ['Id', 'Name', 'Description', 'Target', 'Unit', 'Period', 'Multi', 'Order'];

        table($headers, $list);
    }
}
