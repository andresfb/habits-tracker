<?php

declare(strict_types=1);

namespace App\Console\Interfaces;

use Illuminate\Support\Collection;

interface MenuInterface
{
    /**
     * @return Collection<string|MenuItemInterface>
     */
    public function getMenuItems(): Collection;
}
