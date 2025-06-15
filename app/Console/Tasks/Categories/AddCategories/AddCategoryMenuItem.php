<?php

declare(strict_types=1);

namespace App\Console\Tasks\Categories\AddCategories;

use App\Console\Interfaces\MenuItemInterface;
use App\Console\Interfaces\TaskInterface;

final class AddCategoryMenuItem implements MenuItemInterface
{
    public function itemName(): string
    {
        return 'Add Category';
    }

    public function task(): TaskInterface
    {
        return app(AddCategoryTask::class);
    }
}
