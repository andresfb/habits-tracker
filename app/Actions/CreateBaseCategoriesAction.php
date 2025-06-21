<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Category;
use Illuminate\Support\Facades\Config;

final readonly class CreateBaseCategoriesAction
{
    public function handle(int $userId): void
    {
        $categories = collect(Config::array('categories.base_list'));

        $categories->each(function (array $category) use ($userId): void {
            $category['user_id'] = $userId;
            Category::create($category);
        });
    }
}
