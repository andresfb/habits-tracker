<?php

declare(strict_types=1);

use App\Services\TrackerService;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public array $days;

    private TrackerService $service;

    public function boot(TrackerService $service): void
    {
        $this->service = $service;
    }

    public function mount(): void
    {
        // 1) build a “month view” from the 1st Mon before startOfMonth()
        //    to the last Sun after endOfMonth()
        $start = CarbonImmutable::now()
            ->startOfMonth()
            ->startOfWeek(CarbonInterface::SUNDAY);

        $end = CarbonImmutable::now()
            ->endOfMonth()
            ->endOfWeek(CarbonInterface::MONDAY);

        $this->days = collect(
            CarbonPeriod::create($start, '1 day', $end)
        )->all();
    }

    public function with(): array
    {
        return [
            'habits' => $this->service->getMonthlyTrackers(
                auth()->id(),
                CarbonImmutable::now(),
            ),
        ];
    }
}; ?>

<div class="space-y-6">
    <!-- HEADER -->
    <x-header title="Habits Overview" subtitle="For {{ now()->format('F, Y') }}" separator progress-indicator/>

    <!-- Habits -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($habits as $habit)
        <x-mary-card class="p-4">
            <div class="mb-2 flex items-center">
                <x-dynamic-component
                    :component="$habit->icon"
                    width="20"
                    height="20"
                    class="text-secondary mr-3"/>
                <span class="text-lg font-semibold">
                    {{ $habit->name }}
                </span>
                <div class="text-xs text-muted ml-auto">
                    Goal: {{ $habit->target_value }} {{ mb_strtolower($habit->unit->name) }} per {{ $habit->period->name }}
                </div>
            </div>

            <div class="grid grid-cols-7 text-xs font-semibold uppercase text-gray-500 mt-3 mb-3">
                @foreach(['S','M','T','W','T','F','S'] as $dayLetter)
                    <div class="text-left ml-2">{{ $dayLetter }}</div>
                @endforeach
            </div>

            <div class="grid grid-cols-7 gap-1">
                @foreach($days as $day)
                    @php
                        $entries = $habit->entries->where('logged_at', '>=', $day->startOfDay())
                            ->where('logged_at', '<=', $day->endOfDay())
                            ->collect();

                        $sumValues = 0;
                        if ($entries->isEmpty()) {
                            $status = 'none';
                        } else {
                            $sumValues = $entries->sum('value');
                            $halfWay = $habit->target_value * 0.5;

                            if ($sumValues >= $habit->target_value) {
                                $status = 'complete';
                            } elseif ($sumValues >= $halfWay) {
                                $status = 'partial-high';
                            } else {
                                $status = 'partial-low';
                            }
                        }

                        $bg = [
                          'none' => 'bg-gray-200',
                          'partial-low' => 'bg-emerald-300',
                          'partial-high' => 'bg-emerald-500',
                          'complete'=> 'bg-emerald-700',
                        ][$status];

                        $title = sprintf(
                            '%s - %s %s',
                            $day->format('M j'),
                            $sumValues,
                            Str::of($habit->unit->name)
                                ->singular()
                                ->plural($sumValues)
                                ->lower()
                                ->value(),
                        );
                    @endphp

                    <div class="h-6 w-6 rounded {{ $bg }}" title="{{ $title }}"></div>
                @endforeach
            </div>
        </x-mary-card>
    @endforeach
    </div>
</div>

