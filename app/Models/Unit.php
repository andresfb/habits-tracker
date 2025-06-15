<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

final class Unit extends SluggableModel
{
    use HasFactory;

    public static function getList(): array
    {
        return Cache::remember('units:list', now()->addDay(), static fn () => self::select('id', 'name')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray());
    }

    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }

    protected static function booted(): void
    {
        self::saved(static function (): void {
            Cache::forget('units:list');
        });
    }
}
