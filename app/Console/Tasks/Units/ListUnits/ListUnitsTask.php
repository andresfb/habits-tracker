<?php

declare(strict_types=1);

namespace App\Console\Tasks\Units\ListUnits;

use App\Console\Dtos\TaskResultItem;
use App\Console\Interfaces\TaskInterface;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Collection;

use function Laravel\Prompts\pause;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

final class ListUnitsTask implements TaskInterface
{
    public function handle(): TaskResultItem
    {
        $units = Unit::query()
            ->orderBy('name')
            ->get();

        if ($units->isEmpty()) {
            return new TaskResultItem(
                success: false,
                message: 'No units found.'
            );
        }

        $this->display($units);

        pause('Press ENTER to continue.');

        return new TaskResultItem(
            success: true,
            message: sprintf('Found %s units', $units->count()),
        );
    }

    public function display(Collection $units): void
    {
        if ($units->isEmpty()) {
            warning('No categories found.');

            return;
        }

        $list = $units->map(fn (Unit $unit): array => [
            $unit->id,
            $unit->name,
        ]);

        $headers = ['Id', 'Name'];

        table($headers, $list);
    }
}
