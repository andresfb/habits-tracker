<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Unit extends SlugableModel
{
    use HasFactory;

    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }
}
