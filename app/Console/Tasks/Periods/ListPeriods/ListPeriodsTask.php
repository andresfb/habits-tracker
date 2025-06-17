<?php

declare(strict_types=1);

namespace App\Console\Tasks\Periods\ListPeriods;

use App\Console\Dtos\TaskResultItem;
use App\Console\Interfaces\TaskInterface;
use App\Models\Period;
use Illuminate\Database\Eloquent\Collection;

use function Laravel\Prompts\pause;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

final class ListPeriodsTask implements TaskInterface
{
    public function handle(): TaskResultItem
    {
        $periods = Period::query()
            ->orderBy('interval_days')
            ->get();

        if ($periods->isEmpty()) {
            return new TaskResultItem(
                success: false,
                message: 'No periods found.'
            );
        }

        $this->display($periods);

        pause('Press ENTER to continue.');

        return new TaskResultItem(
            success: true,
            message: 'Found '.$periods->count().' periods.'
        );
    }

    private function display(Collection $periods): void
    {
        if ($periods->isEmpty()) {
            warning('No periods found.');

            return;
        }

        $list = $periods->map(fn (Period $period): array => [
            'id' => $period->id,
            'Name' => $period->name,
            'Interval' => $period->interval_days.' days',
        ])->toArray();

        $headers = ['Id', 'Name', 'Interval'];

        table($headers, $list);
    }
}
