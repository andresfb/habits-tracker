<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property int $habit_id
 * @property int $value
 * @property CarbonImmutable $logged_at
 * @property string $notes
 * @property CarbonImmutable $deleted_at
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 */
final class HabitEntry extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }

    public function value(): Attribute
    {
        return Attribute::make(
            get: static fn (int $val): int|float => $val / 1000,
            set: static fn (float $val): int => (int) round($val * 1000),
        );
    }

    protected static function booted(): void
    {
        self::saved(static function (): void {
            Cache::tags('trackers')->flush();
        });
    }

    protected function casts(): array
    {
        return [
            'logged_at' => 'datetime',
        ];
    }
}
