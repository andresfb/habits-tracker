<?php

namespace App\Services;

use App\Dtos\CategoryItem;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class CategoryService
{
    public function getSelectableList(): array
    {
        return Cache::tags('categories')
            ->remember(
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
        return Cache::tags('categories')
            ->remember(
                'category:list',
                now()->addHour(),
                static fn () => Category::query()
                    ->orderBy('order_by')
                    ->get()
        );
    }

    public function find(int $categoryId): Category
    {
        return Cache::tags('categories')
            ->remember(
                "category:find:$categoryId",
                now()->addHour(),
                static fn () => Category::query()
                    ->where('id', $categoryId)
                    ->first()
        );
    }

    public function create(CategoryItem $category): void
    {
        Category::create(
            $category->toArray()
        );
    }

    public function update(CategoryItem $categoryItem): void
    {
        $category = Category::where('id', $categoryItem->id)
            ->firstOrFail();

        $updated = $category->update(
            $categoryItem->toArray()
        );

        if (! $updated) {
            throw new RuntimeException('Failed to update category');
        }
    }
}
