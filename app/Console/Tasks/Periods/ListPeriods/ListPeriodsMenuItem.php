<?php

namespace App\Console\Tasks\Periods\ListPeriods;

use App\Console\Interfaces\MenuItemInterface;
use App\Console\Interfaces\TaskInterface;

class ListPeriodsMenuItem implements MenuItemInterface
{
    public function itemName(): string
    {
        return 'List Periods';
    }

    public function task(): TaskInterface
    {
        return app(ListPeriodsTask::class);
    }
}
