<?php

declare(strict_types=1);

namespace App\Console\Tasks\Periods;

use App\Console\Interfaces\MenuInterface;
use App\Console\Tasks\Periods\ListPeriods\ListPeriodsMenuItem;
use Illuminate\Support\Collection;

final class PeriodsMenu implements MenuInterface
{
    /**
     * {@inheritDoc}
     */
    public function getMenuItems(): Collection
    {
        return collect([
            app(ListPeriodsMenuItem::class),
        ]);
    }
}
