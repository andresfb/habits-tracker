<?php

namespace App\Console\Interfaces;

interface MenuItemInterface
{
    public function itemName(): string;

    public function task(): TaskInterface;
}
