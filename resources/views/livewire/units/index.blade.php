<?php

use App\Dtos\UnitItem;
use App\Services\UnitService;
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

    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public int $editingUnitId = 0;
    public array $unitForm = [
        'name' => '',
    ];

    private UnitService $service;

    public function boot(UnitService $service): void
    {
        $this->service = $service;
    }

    protected function rules(): array
    {
        return [
            'unitForm.name' => 'required|string',
        ];
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'name', 'label' => 'Name', 'class' => 'w-64'],
        ];
    }

    public function units(): Collection
    {
        return $this->service->getList()
            ->sortBy([[...array_values($this->sortBy)]])
            ->when($this->search, function (Collection $collection) {
                return $collection->filter(fn(array $item) => str($item['name'])->contains($this->search, true));
            });
    }

    public function create(): void
    {
        $this->reset(['unitForm']);
        $this->showCreateModal = true;
    }

    public function edit(int $unitId): void
    {
        $this->editingUnitId = $unitId;
        $unit = $this->service->find($unitId);

        $this->unitForm = [
            'name' => $unit->name,
        ];

        $this->showEditModal = true;
    }

    public function store(): void
    {
        $this->validate();

        $this->service->create(
            UnitItem::from($this->unitForm)
        );

        $this->editingUnitId = 0;
        $this->showCreateModal = false;

        $this->toast(
            type: 'success',
            title: 'Unit added!',
        );
    }

    public function update(): void
    {
        $this->validate();

        try {
            $this->service->update(
                UnitItem::from($this->unitForm)
                    ->withId($this->editingUnitId)
            );
        } catch (Exception $e) {
            $this->toast(
                type: 'error',
                title: 'Error updating',
                description: $e->getMessage(),
            );
        }

        $this->editingUnitId = 0;
        $this->showEditModal = false;

        $this->toast(
            type: 'success',
            title: 'Unit updated!',
        );
    }

    public function with(): array
    {
        return [
            'units' => $this->units(),
            'headers' => $this->headers(),
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Units" separator progress-indicator>
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
        <x-table :headers="$headers" :rows="$units" :sort-by="$sortBy">
            @scope('actions', $unit)
            <x-button icon="o-pencil-square"
                      wire:click="edit({{ $unit['id'] }})"
                      spinner
                      class="btn-ghost btn-lg text-primary"/>
            @endscope
        </x-table>
    </x-card>

    <!-- ADD MODAL -->
    <x-modal wire:model="showCreateModal"
             title="Add Unit"
             persistent
             separator>
        <x-form wire:submit.prevent="store">
            <x-input label="Name"
                     wire:model.defer="unitForm.name"/>

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
             title="Edit Unit"
             persistent
             separator>
        <x-form wire:submit.prevent="update">
            <x-input label="Name"
                     wire:model.defer="unitForm.name"/>

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
