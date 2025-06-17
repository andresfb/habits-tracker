<?php

declare(strict_types=1);

namespace App\Console\Commands;

final class HabitTrackerCommand extends MenuBasedCommand
{
    protected $signature = 'habit:tracker';

    protected $description = 'Console app to track habits';

    public function getTitle(): string
    {
        return 'Habits Tracker';
    }

    public function getTaskKey(): string
    {
        return 'habits-tasks';
    }
}
