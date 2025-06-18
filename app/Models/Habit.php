<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

/**
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property int $unit_id
 * @property int $period_id
 * @property string $name
 * @property string $slug
 * @property string $icon
 * @property string $description
 * @property int $target_value
 * @property bool $allow_multiple_times
 * @property int $order_by
 * @property Unit $unit
 * @property Period $period
 * @property Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class Habit extends SluggableModel
{
    use HasFactory;
    use SoftDeletes;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function hasEntryToday(): bool
    {
        return $this->entries()
            ->whereDate('logged_at', now()->toDateString())
            ->exists();
    }

    public function entries(): HasMany
    {
        return $this->hasMany(HabitEntry::class);
    }

    public function targetValue(): Attribute
    {
        return Attribute::make(
            get: static fn (?int $val): int|float|null => is_null($val) ? null : $val / 1000,
            set: static fn (?float $val): ?int => is_null($val) ? null : (int) round($val * 1000),
        );
    }

    public function defaultValue(): Attribute
    {
        return Attribute::make(
            get: static fn (?int $val): int|float|null => is_null($val) ? null : $val / 1000,
            set: static fn (?float $val): ?int => is_null($val) ? null : (int) round($val * 1000),
        );
    }

    public function icon(): Attribute
    {
        return Attribute::make(
            get: static fn (?string $val): string|null => is_null($val)
                ? Config::string('constants.default_icon')
                : $val,
        );
    }

    protected function casts(): array
    {
        return [
            'allow_multiple_times' => 'boolean',
            'order_by' => 'integer',
        ];
    }
}
