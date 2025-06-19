<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Sluggable\HasSlug;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $interval_days
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class Period extends SluggableModel
{
    use HasFactory;
    use HasSlug;

    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }

    protected static function booted(): void
    {
        self::saved(static function (): void {
            Cache::tags('periods')->flush();
        });
    }

    protected function casts(): array
    {
        return [
            'interval_days' => 'integer',
        ];
    }
}
