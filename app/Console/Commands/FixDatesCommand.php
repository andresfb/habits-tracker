<?php

namespace App\Console\Commands;

use App\Models\HabitEntry;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\intro;

class FixDatesCommand extends Command
{
    protected $signature = 'fix:dates';

    protected $description = 'Command description';

    public function handle(): void
    {
        try {
            clear();
            intro('Fix entry dates');

            $trackers = HabitEntry::all();

            foreach ($trackers as $tracker) {
                $loggedAt = $this->translateDate($tracker->logged_at);
                $createdAt = $this->translateDate($tracker->created_at);
                $updatedAt = $this->translateDate($tracker->updated_at);

                DB::table('habit_entries')
                    ->where('id', $tracker->id)
                    ->update([
                        'logged_at' => $loggedAt,
                        'created_at' => $createdAt,
                        'updated_at' => $updatedAt,
                    ]);

                echo '.';
            }
        } catch (Throwable $throwable) {
            error("\nSomething went wrong:\n".$throwable->getMessage());
        } finally {
            $this->line('');
        }
    }

    private function translateDate(CarbonImmutable $date): string
    {
        return CarbonImmutable::createFromFormat(
            'Y-m-d H:i:s',
            $date->toDateTimeString(),
            'UTC'
        )?->setTimezone('America/New_York')
            ->toDateTimeString();
    }
}
