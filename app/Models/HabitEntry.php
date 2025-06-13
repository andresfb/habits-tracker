<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HabitEntry extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'logged_at' => 'datetime',
        ];
    }

    protected function value(): Attribute
    {
        return Attribute::make(
            get: static fn (int $val): int|float => $val / 1000,
            set: static fn (float $val): int => (int) round($val * 1000),
        );
    }

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }
}
