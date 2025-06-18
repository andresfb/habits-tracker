<?php

use App\Models\Category;
use App\Services\CategoriesService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new
#[Layout('components.layouts.app')]
#[Title('Categories')]
class extends Component {

    public string $search = '';

    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    private CategoriesService $service;

    public function boot(CategoriesService $service): void
    {
        $this->service = $service;
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'name', 'label' => 'Name', 'class' => 'w-56'],
            ['key' => 'color', 'label' => 'Color', 'class' => 'w-8'],
        ];
    }

    public function categories(): Collection
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
            'categories' => $this->categories(),
            'headers' => $this->headers(),
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Categories" separator progress-indicator>
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
        <x-table :headers="$headers" :rows="$categories" :sort-by="$sortBy">
            @scope('cell_color', $category)
            <x-bi-square-fill width="24" height="24" class="text-[{{ $category['color'] }}]" />
            @endscope

            @scope('actions', $category)
            <x-button icon="o-pencil-square"
                      wire:click="delete({{ $category['id'] }})"
                      wire:confirm="Are you sure?"
                      spinner
                      class="btn-ghost btn-lg text-primary"/>
            @endscope
        </x-table>
    </x-card>

</div>
