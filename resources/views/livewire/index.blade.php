<?php

use App\Dtos\TrackerItem;
use App\Models\Habit;
use App\Models\User;
use App\Services\TrackerService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new
#[Layout('components.layouts.app')]
#[Title('Trackers')]
class extends Component {
    use Toast;

    public bool $showEditModal = false;

    public array $entryForm = [
        'id' => '',
        'name' => '',
        'value' => 0.00,
        'default' => 0.00,
        'unit' => '',
    ];

    private TrackerService $service;

    public function boot(TrackerService $service): void
    {
        $this->service = $service;
    }

    protected function rules(): array
    {
        return [
            'entryForm.value' => 'required|numeric',
        ];
    }

    public function show(int $habitId): void
    {
        $habit = $this->service->habitService->find($habitId, auth()->id());
        if (!$habit instanceof Habit) {
            $this->toast('error', 'Habit not found');

            return;
        }

        $this->entryForm = [
            'id' => $habit->id,
            'name' => $habit->name,
            'value' => 0.00,
            'default' => $habit->default_value,
            'unit' => $habit->unit->name,
        ];

        $this->showEditModal = true;
    }

    public function store(int $habitId): void
    {
        $this->validate();

        $this->service->recordCustomEntry(
            $habitId,
            auth()->id(),
            $this->entryForm['value'],
        );

        $this->showEditModal = false;

        $this->toast(
            type: 'success',
            title: 'Entry added!',
        );
    }

    public function addEntry(int $habitId): void
    {
        try {
            $this->service->recordEntry(
                $habitId,
                auth()->id(),
            );
        } catch (Exception $exception) {
            $this->toast(
                type: 'error',
                title: 'Error recording Entry',
                description: $exception->getMessage(),
            );
        }

        $this->toast('success', 'Entry save successfully');
    }

    public function trackers(): Collection
    {
        return $this->service->getDailyTrackers(auth()->id())
            ->map(function (Habit $habit): TrackerItem {
                $entriesTotal = $habit->entries->sum('value');
                $needsEntry = $entriesTotal < $habit->target_value;

                $needsEntryClass = 'text-success';
                if ($needsEntry) {
                    $needsEntryClass = 'text-muted';
                }

                return new TrackerItem(
                    id: $habit->id,
                    name: $habit->name,
                    icon: $habit->icon,
                    subTitle: sprintf(
                        "%d %s of %d in a %s",
                        $entriesTotal,
                        mb_strtolower($habit->unit->name),
                        $habit->target_value,
                        $habit->period->name
                    ),
                    allowMore: $habit->allow_multiple_times,
                    needsEntry: $needsEntry,
                    needsEntryClass: $needsEntryClass,
                );
            });
    }

    public function with(): array
    {
        return [
            'trackers' => $this->trackers(),
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Daily Trackers" subtitle="For {{ now()->toFormattedDateString() }}" separator progress-indicator/>

    <x-card shadow>
        @foreach($trackers as $tracker)
            <x-list-item :item="$tracker" class="mb-1" cursor-pointer>
                <x-slot:avatar wire:click="show({{ $tracker->id }})">
                    <x-dynamic-component
                        :component="$tracker->icon"
                        width="45"
                        height="45"
                        class="text-secondary mr-3"/>
                </x-slot:avatar>
                <x-slot:value wire:click="show({{ $tracker->id }})">
                    {{ $tracker->name }}
                </x-slot:value>
                <x-slot:sub-value class="{{ $tracker->needsEntryClass }}" wire:click="show({{ $tracker->id }})">
                    <div class="w-45 md:w-full whitespace-normal">
                        {{ $tracker->subTitle }}
                    </div>
                </x-slot:sub-value>
                <x-slot:actions>
                    @if($tracker->needsEntry || $tracker->allowMore)
                        <x-button class="btn-ghost p-0 -m-px" wire:click="addEntry({{ $tracker->id }})" spinner="register">
                            <x-bi-square
                                width="35"
                                height="35"
                                class="text-secondary"/>
                        </x-button>
                    @else
                        <x-button class="btn-ghost p-0 -m-px" disabled>
                            <x-bi-check-square width="35" height="35" class="text-success"/>
                        </x-button>
                    @endif
                </x-slot:actions>
            </x-list-item>
        @endforeach
    </x-card>

    <!-- EDIT MODAL -->
    <x-modal wire:model="showEditModal"
             title="Custom Entry for {{ $entryForm['name'] }}"
             persistent
             separator>
        <x-form wire:submit.prevent="store({{ $entryForm['id'] }})">

            <x-input label="Enter Value"
                     wire:model.defer="entryForm.value"
                     type="number"
                     hint="{{ $entryForm['default'] . ' ' . $entryForm['unit'] }}"
                     suffix="{{ $entryForm['unit'] }}"/>

            <x-slot:actions>
                <x-button label="Cancel"
                          @click="$wire.showEditModal = false"/>
                <x-button type="submit"
                          label="Save"
                          class="btn-primary"/>
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
