<?php

namespace App\Services;

use App\Models\Habit;
use Illuminate\Database\Eloquent\Collection;

class HabitsService
{
    public function getList(int $userId): Collection
    {
        return Habit::query()
            ->with([
                'user',
                'unit',
                'period',
                'category',
                'entries',
            ])
            ->where('user_id', $userId)
            ->orderBy('order_by')
            ->get();
    }
}
