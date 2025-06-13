<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;

final class Period extends SlugableModel
{
    use HasFactory;
    use HasSlug;

    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }

    protected function casts(): array
    {
        return [
            'interval_days' => 'integer',
        ];
    }
}
