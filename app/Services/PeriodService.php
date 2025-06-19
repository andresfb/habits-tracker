<?php

namespace App\Services;

use App\Dtos\PeriodItem;
use App\Models\Period;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class PeriodService
{
    public function getSelectableList(): array
    {
        return Cache::tags('periods')
            ->remember(
                'period:selectable:list',
                now()->addDay(),
                static fn() => Period::select('id', 'name')
                    ->orderBy('name')
                    ->get()
                ->pluck('name', 'id')
                ->toArray()
            );
    }

    public function getList(): Collection
    {
        return Cache::tags('periods')
            ->remember(
                'period:list',
                now()->addHour(),
                static fn () => Period::query()
                    ->orderBy('interval_days')
                    ->get()
            );
    }

    public function find(int $periodId): Period
    {
        return Cache::tags('periods')
            ->remember(
                "period:find:{$periodId}",
                now()->addHour(),
                static fn () => Period::query()
                    ->where('id', $periodId)
                    ->first()
            );
    }

    public function create(PeriodItem $periodItem): void
    {
        Period::create(
            $periodItem->toArray()
        );
    }

    public function update(PeriodItem $periodItem): void
    {
        $period = Period::where('id', $periodItem->id)
            ->firstOrFail();

        $updated = $period->update(
            $periodItem->toArray()
        );

        if (! $updated) {
            throw new RuntimeException('Failed to update period');
        }
    }
}
