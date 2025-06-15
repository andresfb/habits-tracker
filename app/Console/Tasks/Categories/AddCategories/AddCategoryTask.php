<?php

declare(strict_types=1);

namespace App\Console\Tasks\Categories\AddCategories;

use App\Console\Dtos\TaskResultItem;
use App\Console\Interfaces\TaskInterface;
use App\Console\Services\AuthService;
use App\Console\Tasks\Categories\ListCategories\ListCategoriesTask;
use App\Console\Traits\Colorable;
use App\Models\Category;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\form;
use function Laravel\Prompts\info;

final readonly class AddCategoryTask implements TaskInterface
{
    use Colorable;

    public function __construct(private ListCategoriesTask $categoriesTask) {}

    public function handle(): TaskResultItem
    {
        $user = AuthService::user();

        $categories = $this->categoriesTask->getCategories($user);

        $this->categoriesTask->displayCategories($categories);

        info('Create a Category');

        $nextOrder = Category::query()
            ->where('user_id', $user->getAuthIdentifier())
            ->max('order_by');

        $response = form()
            ->text(
                label: 'Name:',
                required: true,
                validate: 'string|max:100',
                name: 'name',
            )
            ->select(
                label: 'Select a color:',
                options: $this->getColors(),
                scroll: 15,
                name: 'color',
            )
            ->text(
                label: 'Order:',
                default: (string) ($nextOrder + 1),
                required: true,
                validate: 'integer',
                name: 'order_by',
            )
            ->submit();

        $slug = str($response['name'])->slug();
        if (Category::where('slug', $slug)->exists()) {
            error('Category already exists.');

            if (confirm('Try again?')) {
                return $this->handle();
            }

            return new TaskResultItem(false, 'Category creation cancelled.');
        }

        $category = Category::create([
            'user_id' => $user->getAuthIdentifier(),
            'name' => $response['name'],
            'color' => $response['color'],
            'order_by' => $response['order_by'],
        ]);

        if ($category === null) {
            return new TaskResultItem(false, 'Could not create category.');
        }

        if (confirm('Do you want to add another category?')) {
            return $this->handle();
        }

        return new TaskResultItem(
            success: true,
            message: 'Category created successfully.'
        );
    }
}
