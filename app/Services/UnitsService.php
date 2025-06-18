<?php

namespace App\Services;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class UnitsService
{
    public function getSelectableList(): array
    {
        return Cache::remember(
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
        return Cache::remember(
            'units:list',
            now()->addHour(),
            static fn () => Unit::query()
                ->orderBy('name')
                ->get()
        );
    }
}
