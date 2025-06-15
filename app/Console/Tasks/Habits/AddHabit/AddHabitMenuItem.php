<?php

declare(strict_types=1);

namespace App\Console\Tasks\Habits\AddHabit;

use App\Console\Interfaces\MenuItemInterface;
use App\Console\Interfaces\TaskInterface;

class AddHabitMenuItem implements MenuItemInterface
{
    public function itemName(): string
    {
        return 'Add Habit';
    }

    public function task(): TaskInterface
    {
        return app(AddHabitTask::class);
    }
}
