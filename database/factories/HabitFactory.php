<?php

namespace Database\Factories;

use App\Models\Habit;
use App\Models\Period;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class HabitFactory extends Factory
{
    protected $model = Habit::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(),
            'category_id' => $this->faker->randomNumber(),
            'name' => $this->faker->name(),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->text(),
            'target_value' => $this->faker->randomNumber(),
            'allow_multiple_times' => $this->faker->boolean(),
            'order_by' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'notes' => $this->faker->word(),
            'default_value' => $this->faker->randomFloat(),

            'unit_id' => Unit::factory(),
            'period_id' => Period::factory(),
        ];
    }
}
