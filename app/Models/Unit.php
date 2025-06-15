<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

final class Unit extends SluggableModel
{
    use HasFactory;

    protected static function booted(): void
    {
        self::saved(static function () {
            Cache::forget('units:list');
        });
    }

    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }

    public static function getList(): array
    {
        return Cache::remember('units:list', now()->addDay(), static function () {
            return self::select('id', 'name')
                ->orderBy('name')
                ->get()
                ->pluck('name', 'id')
                ->toArray();
        });
    }
}
