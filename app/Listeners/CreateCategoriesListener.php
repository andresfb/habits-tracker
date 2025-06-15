<?php

namespace App\Listeners;

use App\Models\Category;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Config;

class CreateCategoriesListener implements ShouldQueue
{
    public function handle(Registered $event): void
    {
        $categories = collect(Config::array('categories.base_list'));

        $categories->each(function ($category) use ($event) {
            $category['user_id'] = $event->user->getAuthIdentifier();
            Category::create($category);
        });
    }
}
