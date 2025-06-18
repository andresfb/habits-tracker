<?php

namespace App\Services;

use App\Models\Period;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class PeriodsService
{
    public function getSelectableList(): array
    {
        return Cache::remember(
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
        return Cache::remember(
            'period:list',
            now()->addHour(),
            static fn () => Period::query()
                ->orderBy('interval_days')
                ->get()
        );
    }
}
