<?php

namespace App\Console\Interfaces;

use Illuminate\Support\Collection;

interface MenuInterface
{
    /**
     * @return Collection<String|MenuItemInterface>
     */
    public function getMenuItems(): Collection;
}
