<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Spatie\Sluggable\HasSlug;

final class Period extends SluggableModel
{
    use HasFactory;
    use HasSlug;

    protected static function booted(): void
    {
        self::saved(static function (): void {
            Cache::forget('period:list');
        });
    }

    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }

    public static function getList(): array
    {
        return Cache::remember('period:list', now()->addDay(), static fn() => self::select('id', 'name')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray());
    }

    protected function casts(): array
    {
        return [
            'interval_days' => 'integer',
        ];
    }
}
