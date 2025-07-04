<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Habit;
use App\Models\HabitEntry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<HabitEntry>
 */
final class HabitEntryFactory extends Factory
{
    protected $model = HabitEntry::class;

    public function definition(): array
    {
        return [
            'value' => fake()->randomFloat(),
            'logged_at' => Carbon::now(),
            'notes' => fake()->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'habit_id' => Habit::factory(),
        ];
    }
}
