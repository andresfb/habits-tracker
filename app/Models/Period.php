<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;

class Period extends SlugableModel
{
    use HasFactory;
    use HasSlug;

    protected function casts(): array
    {
        return [
            'interval_days' => 'integer',
        ];
    }

    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }
}
