<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

final class Category extends SluggableModel
{
    use HasFactory;
    use SoftDeletes;

    protected static function booted(): void
    {
        self::saved(static function (): void {
            Cache::forget('category:list');
        });
    }


    protected function casts(): array
    {
        return [
            'order_by' => 'integer'
        ];
    }

    public static function getList(): array
    {
        return Cache::remember('category:list', now()->addDay(), static fn() => self::select('id', 'name')
            ->orderBy('order_by')
            ->get()
            ->pluck('name', 'id')
            ->toArray());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function habits(): BelongsToMany
    {
        return $this->belongsToMany(Habit::class, 'category_habit');
    }
}
