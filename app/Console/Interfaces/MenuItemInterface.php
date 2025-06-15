<?php

declare(strict_types=1);

namespace App\Console\Interfaces;

interface MenuItemInterface
{
    public function itemName(): string;

    public function task(): TaskInterface;
}
