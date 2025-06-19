<?php

namespace Database\Factories;

use App\Models\Habit;
use App\Models\Period;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Habit>
 */
class HabitFactory extends Factory
{
    protected $model = Habit::class;

    public function definition(): array
    {
        return [
            'user_id' => fake()->randomNumber(),
            'category_id' => fake()->randomNumber(),
            'name' => fake()->name(),
            'slug' => fake()->slug(),
            'description' => fake()->text(),
            'target_value' => fake()->randomNumber(),
            'allow_multiple_times' => fake()->boolean(),
            'order_by' => fake()->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'notes' => fake()->word(),
            'default_value' => fake()->randomFloat(),

            'unit_id' => Unit::factory(),
            'period_id' => Period::factory(),
        ];
    }
}
