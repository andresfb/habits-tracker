<?php

declare(strict_types=1);

namespace App\Console\Tasks\Categories;

use App\Console\Interfaces\MenuInterface;
use App\Console\Tasks\Categories\AddCategories\AddCategoryMenuItem;
use App\Console\Tasks\Categories\ListCategories\ListCategoryMenuItem;
use Illuminate\Support\Collection;

class CategoriesMenu implements MenuInterface
{
    /**
     * @inheritDoc
     */
    public function getMenuItems(): Collection
    {
        return collect([
            app(ListCategoryMenuItem::class),
            app(AddCategoryMenuItem::class),
        ]);
    }
}
