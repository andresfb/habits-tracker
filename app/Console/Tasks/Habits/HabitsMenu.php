<?php

declare(strict_types=1);

namespace App\Console\Tasks\Habits;

use App\Console\Interfaces\MenuInterface;
use App\Console\Tasks\Habits\AddHabit\AddHabitMenuItem;
use App\Console\Tasks\Habits\ListHabits\ListHabitsMenuItem;
use Illuminate\Support\Collection;

final class HabitsMenu implements MenuInterface
{
    /**
     * {@inheritDoc}
     */
    public function getMenuItems(): Collection
    {
        return collect([
            app(ListHabitsMenuItem::class),
            app(AddHabitMenuItem::class),
        ]);
    }
}
