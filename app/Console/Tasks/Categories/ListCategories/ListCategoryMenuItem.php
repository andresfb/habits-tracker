<?php

declare(strict_types=1);

namespace App\Console\Tasks\Categories\ListCategories;

use App\Console\Interfaces\MenuItemInterface;
use App\Console\Interfaces\TaskInterface;

final class ListCategoryMenuItem implements MenuItemInterface
{
    public function itemName(): string
    {
        return 'List Categories';
    }

    public function task(): TaskInterface
    {
        return app(ListCategoriesTask::class);
    }
}
