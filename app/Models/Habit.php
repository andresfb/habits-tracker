<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Habit extends SluggableModel
{
    use HasFactory;
    use SoftDeletes;

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

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_habit');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(HabitEntry::class);
    }

    protected function casts(): array
    {
        return [
            'allow_multiple_times' => 'boolean',
            'order_by' => 'integer',
        ];
    }

    protected function targetValue(): Attribute
    {
        return Attribute::make(
            get: static fn (?int $val): int|float|null => is_null($val) ? null : $val / 1000,
            set: static fn (?float $val): ?int => is_null($val) ? null : (int) round($val * 1000),
        );
    }
}
