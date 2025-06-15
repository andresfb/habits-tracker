<?php

declare(strict_types=1);

namespace App\Console\Tasks\Units\ListUnits;

use App\Console\Interfaces\MenuItemInterface;
use App\Console\Interfaces\TaskInterface;

class ListUnitsMenuItem implements MenuItemInterface
{
    public function itemName(): string
    {
        return 'List Units';
    }

    public function task(): TaskInterface
    {
        return app(ListUnitsTask::class);
    }
}
