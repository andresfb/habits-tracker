<?php

use App\Dtos\CategoryItem;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new
#[Layout('components.layouts.app')]
#[Title('Categories')]
class extends Component {

    use Toast;

    public string $search = '';
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public int $editingCategoryId = 0;
    public array $categoryForm = [
        'name'  => '',
        'color' => '#000000',
    ];

    private CategoryService $service;

    public function boot(CategoryService $service): void
    {
        $this->service = $service;
    }

    // validation rules (reused by store() & update())
    protected function rules(): array
    {
        return [
            'categoryForm.name' => 'required|string',
            'categoryForm.color' => [
                'required',
                'regex:/^#([A-Fa-f0-9]{6})$/'
            ],
        ];
    }

    // custom error message for the hex rule
    protected array $messages = [
        'categoryForm.color.regex' =>
            'The color must be a valid 6-digit hex code, e.g. #A1B2C3.',
    ];

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

    // → Show the “Add” modal
    public function create(): void
    {
        $this->reset(['categoryForm']);
        $this->categoryForm['color'] = '#000000';
        $this->showCreateModal = true;
    }

    // → Show the “Edit” modal & load model data
    public function edit(int $categoryId): void
    {
        $this->editingCategoryId = $categoryId;
        $category = $this->service->find($categoryId);

        $this->categoryForm = [
            'name'  => $category->name,
            'color' => $category->color,
        ];

        $this->showEditModal = true;
    }

    // → Persist new category
    public function store(): void
    {
        $this->validate();

        $this->service->create(
            CategoryItem::from($this->categoryForm)
        );

        $this->editingCategoryId = 0;
        $this->showCreateModal = false;

        $this->toast(
            type: 'success',
            title: 'Category added!',
        );
    }

    // → Persist updated category
    public function update(): void
    {
        $this->validate();

        try {
            $this->service->update(
                CategoryItem::from($this->categoryForm)
                    ->withId($this->editingCategoryId)
            );
        } catch (Exception $e) {
            $this->toast(
                type: 'error',
                title: 'Error updating',
                description: $e->getMessage(),
            );
        }

        $this->editingCategoryId = 0;
        $this->showEditModal = false;

        $this->toast(
            type: 'success',
            title: 'Category updated!',
        );
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
                      icon="o-plus-circle"
                      wire:click="create"
                      responsive
                      tooltip="Add New"
                      tooltip-left="true"/>
        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$categories" :sort-by="$sortBy">
            @scope('cell_color', $category)
            <x-bi-square-fill width="24" height="24" class="text-[{{ $category['color'] }}]"/>
            @endscope

            @scope('actions', $category)
            <x-button icon="o-pencil-square"
                      wire:click="edit({{ $category['id'] }})"
                      spinner
                      class="btn-ghost btn-lg text-primary"/>
            @endscope
        </x-table>
    </x-card>

    <!-- ADD MODAL -->
    <x-modal wire:model="showCreateModal"
             title="Add Category"
             persistent
             separator>
        <x-form wire:submit.prevent="store">
            <x-input label="Name"
                     wire:model.defer="categoryForm.name"/>

            <x-colorpicker label="Color"
                           icon="o-swatch"
                           suffix="Hex code"
                           wire:model.defer="categoryForm.color"/>

            <x-slot:actions>
                <x-button label="Cancel"
                          @click="$wire.showCreateModal = false"/>
                <x-button type="submit"
                          label="Create"
                          class="btn-primary"/>
            </x-slot:actions>
        </x-form>
    </x-modal>

    <!-- EDIT MODAL -->
    <x-modal wire:model="showEditModal"
             title="Edit Category"
             persistent
             separator>
        <x-form wire:submit.prevent="update">
            <x-input label="Name"
                     wire:model.defer="categoryForm.name"/>

            <x-colorpicker label="Color"
                            icon="o-swatch"
                            suffix="Hex code"
                            wire:model.defer="categoryForm.color"/>

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
