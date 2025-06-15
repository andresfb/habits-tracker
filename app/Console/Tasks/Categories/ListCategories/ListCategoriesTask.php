<?php

declare(strict_types=1);

namespace App\Console\Tasks\Categories\ListCategories;

use App\Console\Dtos\TaskResultItem;
use App\Console\Interfaces\TaskInterface;
use App\Console\Services\AuthService;
use App\Console\Traits\Colorable;
use App\Models\Category;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;

use function Laravel\Prompts\pause;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

final class ListCategoriesTask implements TaskInterface
{
    use Colorable;

    public function handle(): TaskResultItem
    {
        $user = AuthService::user();

        $categories = $this->getCategories($user);

        if ($categories->isEmpty()) {
            return new TaskResultItem(
                success: false,
                message: 'No categories found.'
            );
        }

        $this->displayCategories($categories);

        pause('Press ENTER to continue.');

        return new TaskResultItem(
            success: true,
            message: sprintf('Found %d categories', $categories->count()),
        );
    }

    public function getCategories(Authenticatable $user): Collection
    {
        return Category::query()
            ->where('user_id', $user->getAuthIdentifier())
            ->orderBy('order_by')
            ->get();
    }

    public function displayCategories(Collection $categories): void
    {
        if ($categories->isEmpty()) {
            warning('No categories found.');

            return;
        }

        $list = $categories->map(fn (Category $category): array => [
            'id' => $category->id,
            'Name' => $category->name,
            'Color' => $this->getColor($category->color),
            'Order' => $category->order_by,
        ])->toArray();

        $headers = ['Id', 'Name', 'Color', 'Order'];

        table($headers, $list);
    }

    private function getColor(string $color): string
    {
        $colors = $this->getColors();

        return $colors[$color] ?? $color;
    }
}
