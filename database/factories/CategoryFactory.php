<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'slug' => fake()->slug(),
            'color' => fake()->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'order_by' => fake()->randomNumber(),

            'user_id' => User::factory(),
        ];
    }
}
