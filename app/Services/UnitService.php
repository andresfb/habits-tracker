<?php

namespace App\Services;

use App\Dtos\UnitItem;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class UnitService
{
    public function getSelectableList(): array
    {
        return Cache::tags('units')
            ->remember(
                'units:selectable:list',
                now()->addDay(),
                static fn() => Unit::select('id', 'name')
                    ->orderBy('name')
                    ->get()
                    ->pluck('name', 'id')
                ->toArray()
        );
    }

    public function getList(): Collection
    {
        return Cache::tags('units')
            ->remember(
                'units:list',
                now()->addHour(),
                static fn () => Unit::query()
                    ->orderBy('name')
                    ->get()
            );
    }

    public function find(int $unitId): Unit
    {
        return Cache::tags('units')
            ->remember(
                "unit:find:$unitId",
                now()->addHour(),
                static fn () => Unit::query()
                    ->where('id', $unitId)
                    ->first()
            );
    }

    public function create(UnitItem $item): void
    {
        Unit::create(
            $item->toArray()
        );
    }

    public function update(UnitItem $unitItem): void
    {
        $unit = Unit::where('id', $unitItem->id)
            ->firstOrFail();

        $updated = $unit->update(
            $unitItem->toArray()
        );

        if (! $updated) {
            throw new RuntimeException('Failed to update unit');
        }
    }
}
