<?php

use App\Services\HabitsService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new
#[Layout('components.layouts.app')]
#[Title('Habits')]
class extends Component {

    private HabitsService $service;

    public function boot(HabitsService $service): void
    {
        $this->service = $service;
    }

    public function with(): array
    {
        return [
            'habits' => $this->service->getList(auth()->id()),
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Habits" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Add New"
                      class="btn-success text-gray-100"
                      @click="$wire.drawer = true"
                      responsive
                      icon="o-plus-circle"
                      tooltip="Add New"
                      tooltip-left="true"/>
        </x-slot:actions>
    </x-header>

    <div class="flex flex-wrap -mx-2">
        @foreach($habits as $habit)
            <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 px-2 mb-4">
                <x-card class="h-full" shadow separator>
                    <x-slot:title>
                        <div class="flex justify-items-start">
                            <x-dynamic-component
                                 :component="$habit->icon"
                                 width="45"
                                 height="45"
                                 class="text-secondary mr-3" />
                            <span class="mt-1">
                                {{ $habit->name }}
                            </span>
                        </div>
                    </x-slot:title>

                    <x-slot:subtitle>
                         {{ $habit->description }}
                    </x-slot:subtitle>

                    <dl class="space-y-2">
                        @foreach([
                              'Target Value' => "$habit->target_value {$habit->unit->name}",
                              'Default Value' => "$habit->default_value {$habit->unit->name}",
                              'Period' => $habit->period->name,
                              'Allow Multiple' => $habit->allow_multiple_times ? '✅' : '🚫',
                              'Total Entries' => $habit->entries->count() ?: '—',
                          ] as $label => $value)
                            <div class="flex justify-between items-center">
                                <dt class="text-sm font-medium text-gray-500">{{ $label }}</dt>
                                <dd class="text-sm text-gray-900">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                    <x-slot:actions separator>
                        <x-button label="Edit" class="btn-primary" />
                    </x-slot:actions>
                </x-card>
            </div>
        @endforeach
    </div>

</div>
