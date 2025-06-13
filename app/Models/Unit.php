<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends SlugableModel
{
    use HasFactory;

    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }
}
