<?php

namespace Database\Factories;

use App\Models\Habit;
use App\Models\HabitEntry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class HabitEntryFactory extends Factory
{
    protected $model = HabitEntry::class;

    public function definition(): array
    {
        return [
            'value' => $this->faker->randomFloat(),
            'logged_at' => Carbon::now(),
            'notes' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'habit_id' => Habit::factory(),
        ];
    }
}
