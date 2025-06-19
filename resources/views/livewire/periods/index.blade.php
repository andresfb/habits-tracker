<?php

use App\Dtos\PeriodItem;
use App\Services\PeriodService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new
#[Layout('components.layouts.app')]
#[Title('Units')]
class extends Component {

    use Toast;

    public string $search = '';

    public array $sortBy = ['column' => 'interval_days', 'direction' => 'asc'];

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public int $editingPeriodId = 0;
    public array $periodForm = [
        'name' => '',
        'interval_days' => 1,
    ];

    private PeriodService $service;

    public function boot(PeriodService $service): void
    {
        $this->service = $service;
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'name', 'label' => 'Name', 'class' => 'w-56'],
            ['key' => 'interval_days', 'label' => 'Interval', 'class' => 'w-16'],
        ];
    }

    protected function rules(): array
    {
        return [
            'periodForm.name' => 'required|string',
            'periodForm.interval_days' => 'required|integer'
        ];
    }

    public function periods(): Collection
    {
        return $this->service->getList()
            ->sortBy([[...array_values($this->sortBy)]])
            ->when($this->search, function (Collection $collection) {
                return $collection->filter(fn(array $item) => str($item['name'])->contains($this->search, true));
            });
    }

    public function create(): void
    {
        $this->reset(['periodForm']);
        $this->showCreateModal = true;
    }

    public function edit(int $periodId): void
    {
        $this->editingPeriodId = $periodId;
        $period = $this->service->find($periodId);

        $this->periodForm = [
            'name' => $period->name,
            'interval_days' => $period->interval_days,
        ];

        $this->showEditModal = true;
    }

    public function store(): void
    {
        $this->validate();

        $this->service->create(
            PeriodItem::from($this->periodForm)
        );

        $this->editingPeriodId = 0;
        $this->showCreateModal = false;

        $this->toast(
            type: 'success',
            title: 'Period added!',
        );
    }

    public function update(): void
    {
        $this->validate();

        try {
            $this->service->update(
                PeriodItem::from($this->periodForm)
                    ->withId($this->editingPeriodId)
            );
        } catch (Exception $e) {
            $this->toast(
                type: 'error',
                title: 'Error updating',
                description: $e->getMessage(),
            );
        }

        $this->editingPeriodId = 0;
        $this->showEditModal = false;

        $this->toast(
            type: 'success',
            title: 'Period updated!',
        );
    }

    public function with(): array
    {
        return [
            'periods' => $this->periods(),
            'headers' => $this->headers(),
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Periods" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Add New"
                      class="btn-success text-gray-100"
                      wire:click="create"
                      responsive
                      icon="o-plus-circle"
                      tooltip="Add New"
                      tooltip-left="true"/>
        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$periods" :sort-by="$sortBy">
            @scope('actions', $period)
            <x-button icon="o-pencil-square"
                      wire:click="edit({{ $period['id'] }})"
                      spinner
                      class="btn-ghost btn-lg text-primary"/>
            @endscope
        </x-table>
    </x-card>

    <!-- ADD MODAL -->
    <x-modal wire:model="showCreateModal"
             title="Add Period"
             persistent
             separator>
        <x-form wire:submit.prevent="store">
            <x-input label="Name"
                     wire:model.defer="periodForm.name"/>

            <x-input label="Interval Days"
                     numeric
                     wire:model.defer="periodForm.interval_days"/>

            <x-slot:actions>
                <x-button label="Cancel"
                          @click="$wire.showCreateModal = false"/>
                <x-button type="submit"
                          label="Save"
                          class="btn-primary"/>
            </x-slot:actions>
        </x-form>
    </x-modal>

    <!-- EDIT MODAL -->
    <x-modal wire:model="showEditModal"
             title="Edit Period"
             persistent
             separator>
        <x-form wire:submit.prevent="update">
            <x-input label="Name"
                     wire:model.defer="periodForm.name"/>

            <x-input label="Interval Days"
                     numeric
                     wire:model.defer="periodForm.interval_days"/>

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
