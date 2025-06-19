<?php

namespace App\Services;

use App\Dtos\HabitItem;
use App\Models\Habit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class HabitService
{
    public function getList(int $userId): Collection
    {
        return Cache::tags('habits')
            ->remember(
                "habits:list",
                now()->addHour(),
                static fn () => Habit::query()
                    ->withInfo()
                    ->with('entries')
                    ->where('user_id', $userId)
                    ->orderBy('order_by')
                    ->get()
            );
    }

    public function find(int $habitId, int $userId): ?Habit
    {
        return Cache::tags('habits')
            ->remember(
                "habit:find:{$habitId}:{$userId}",
                now()->addHour(),
                static fn () => Habit::query()
                    ->withInfo()
                    ->with('entries')
                    ->where('user_id', $userId)
                    ->where('id', $habitId)
                    ->first()
            );
    }

    public function create(HabitItem $habitItem): void
    {
        Habit::create(
            $habitItem->toArray()
        );
    }

    /**
     * @throws RuntimeException
     */
    public function update(HabitItem $habitItem): void
    {
        $habit = Habit::where('id', $habitItem->id)
            ->where('user_id', $habitItem->user_id)
            ->firstOrFail();

        $updated = $habit->update(
            $habitItem->toArray()
        );

        if (! $updated) {
            throw new RuntimeException('Failed to update habit');
        }
    }
}
