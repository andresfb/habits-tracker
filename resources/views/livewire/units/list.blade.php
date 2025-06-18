<?php

use App\Services\UnitsService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new
#[Layout('components.layouts.app')]
#[Title('Units')]
class extends Component {

    public string $search = '';

    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    private UnitsService $service;

    public function boot(UnitsService $service): void
    {
        $this->service = $service;
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
                      @click="$wire.drawer = true"
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
                      wire:click="delete({{ $unit['id'] }})"
                      wire:confirm="Are you sure?"
                      spinner
                      class="btn-ghost btn-lg text-primary"/>
            @endscope
        </x-table>
    </x-card>
</div>
