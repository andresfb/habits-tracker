<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\Category;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Config;

final class CreateCategoriesListener implements ShouldQueue
{
    public function handle(Registered $event): void
    {
        $categories = collect(Config::array('categories.base_list'));

        $categories->each(function (array $category) use ($event): void {
            $category['user_id'] = $event->user->getAuthIdentifier();
            Category::create($category);
        });
    }
}
