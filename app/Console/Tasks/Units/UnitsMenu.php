<?php

declare(strict_types=1);

namespace App\Console\Tasks\Units;

use App\Console\Interfaces\MenuInterface;
use App\Console\Tasks\Units\ListUnits\ListUnitsMenuItem;
use Illuminate\Support\Collection;

class UnitsMenu implements MenuInterface
{
    /**
     * @inheritDoc
     */
    public function getMenuItems(): Collection
    {
        return collect([
            app(ListUnitsMenuItem::class),
        ]);
    }
}
