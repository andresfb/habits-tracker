<?php

namespace App\Console\Tasks\Units\ListUnits;

use App\Console\Dtos\TaskResultItem;
use App\Console\Interfaces\TaskInterface;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Collection;
use function Laravel\Prompts\pause;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

class ListUnitsTask implements TaskInterface
{
    public function handle(): TaskResultItem
    {
        $units = Unit::get();

        if ($units->isEmpty()) {
            return new TaskResultItem(
                success: false,
                message: 'No units found.'
            );
        }

        $this->displayUnits($units);

        pause('Press ENTER to continue.');

        return new TaskResultItem(
            success: true,
            message: "Found {$units->count()} units",
        );
    }

    public function displayUnits(Collection $units): void
    {
        if ($units->isEmpty()) {
            warning('No categories found.');

            return;
        }

        $list = $units->map(function (Unit $unit) {
            return [
                'id' => $unit->id,
                'Name' => $unit->name,
            ];
        })->toArray();

        $headers = ['Id', 'Name'];

        table($headers, $list);
    }
}
