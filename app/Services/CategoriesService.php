<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class CategoriesService
{
    public function getSelectableList(): array
    {
        return Cache::remember(
            'category:selectable:list',
            now()->addDay(),
            static fn () => Category::select('id', 'name')
                ->orderBy('order_by')
                ->get()
                ->pluck('name', 'id')
                ->toArray()
        );
    }

    public function getList(): Collection
    {
        return Cache::remember(
            'category:list',
            now()->addHour(),
            static fn () => Category::query()
                ->orderBy('order_by')
                ->get()
        );
    }
}
