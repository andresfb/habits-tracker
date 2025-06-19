<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Habit;
use App\Models\HabitEntry;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

final readonly class TrackerService
{
    public function __construct(
        public HabitService $habitService,
    ) {}

    public function getDailyTrackers(int $userId, ?CarbonImmutable $loggDate = null): Collection
    {
        return Cache::tags('trackers')
            ->remember(
                "daily:trackers:{$userId}",
                now()->addMinutes(15),
                static fn () => Habit::query()
                    ->withInfo()
                    ->withEntriesOnDay($loggDate)
                    ->where('user_id', $userId)
                    ->orderBy('order_by')
                    ->get()
            );
    }

    public function getMonthlyTrackers(int $userId, CarbonImmutable $loggDate = null): Collection
    {
        return Cache::tags('trackers')
            ->remember(
                "monthly:trackers:{$userId}",
                now()->addHour(),
                static fn () => Habit::query()
                    ->withInfo()
                    ->withEntriesOnMonth($loggDate)
                    ->where('user_id', $userId)
                    ->orderBy('order_by')
                    ->get()
            );
    }

    public function recordEntry(int $habitId, int $userId): void
    {
        $habit = $this->habitService->find($habitId, $userId);
        if (! $habit instanceof Habit) {
            throw new RuntimeException('Habit not found');
        }

        HabitEntry::create([
            'habit_id' => $habit->id,
            'value' => $habit->default_value,
            'logged_at' => now(),
            'notes' => 'Default Entry',
        ]);
    }

    public function recordCustomEntry(int $habitId, int $userId, float $value): void
    {
        $habit = $this->habitService->find($habitId, $userId);
        if (! $habit instanceof Habit) {
            throw new RuntimeException('Habit not found');
        }

        HabitEntry::create([
            'habit_id' => $habit->id,
            'value' => $value,
            'logged_at' => now(),
            'notes' => 'Custom Entry',
        ]);
    }
}
