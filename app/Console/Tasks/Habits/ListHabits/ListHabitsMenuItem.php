<?php

declare(strict_types=1);

namespace App\Console\Tasks\Habits\ListHabits;

use App\Console\Interfaces\MenuItemInterface;
use App\Console\Interfaces\TaskInterface;

final class ListHabitsMenuItem implements MenuItemInterface
{
    public function itemName(): string
    {
        return 'List Habits';
    }

    public function task(): TaskInterface
    {
        return app(ListHabitsTask::class);
    }
}
