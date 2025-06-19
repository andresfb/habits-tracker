<?php

use App\Dtos\HabitItem;
use App\Models\Habit;
use App\Services\HabitService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new
#[Layout('components.layouts.app')]
#[Title('Habits')]
class extends Component {

    use Toast;

    public bool $showCreateDrawer = false;
    public bool $showEditDrawer = false;
    public int $editingHabitId = 0;
    public array $habitForm = [
        'category_id' => 1,
        'unit_id' => 1,
        'period_id' => 1,
        'name' => '',
        'description' => '',
        'icon' => '',
        'target_value' => 0.00,
        'default_value' => 0.00,
        'allow_multiple_times' => 0,
        'notes' => '',
        'order_by' => 0,
    ];

    private HabitService $service;

    public function boot(HabitService $service): void
    {
        $this->service = $service;
    }

    protected function rules(): array
    {
        return [
            'habitForm.category_id' => 'required|integer|exists:categories,id',
            'habitForm.unit_id' => 'required|integer|exists:units,id',
            'habitForm.period_id' => 'required|integer|exists:periods,id',
            'habitForm.name' => 'required|string',
            'habitForm.description' => 'nullable|string',
            'habitForm.icon' => 'nullable|string',
            'habitForm.target_value' => 'required|numeric',
            'habitForm.default_value' => 'required|numeric',
            'habitForm.allow_multiple_times' => 'required|boolean',
            'habitForm.notes' => 'nullable|string',
            'habitForm.order_by' => 'required|integer',
        ];
    }

    public function create(): void
    {
        $this->reset(['habitForm']);
        $this->habitForm['category_id'] = 1;
        $this->habitForm['unit_id'] = 1;
        $this->habitForm['period_id'] = 1;
        $this->habitForm['allow_multiple_times'] = false;
        $this->habitForm['icon'] = Config::string('constants.default_icon');
        $this->habitForm['order_by'] = Habit::max('order_by');
        $this->showCreateDrawer = true;
    }

    // → Show the “Edit” modal & load model data
    public function edit(int $habitId): void
    {
        $this->editingHabitId = $habitId;
        $habit = $this->service->find(
            $habitId,
            auth()->id(),
        );

        if (!$habit instanceof Habit) {
            $this->toast(
                type: 'error',
                title: 'Habit not found',
            );

            return;
        }

        $this->habitForm = $habit->toArray();
        $this->showEditDrawer = true;
    }

    // → Persist new category
    public function store(): void
    {
        $this->validate();

        $this->service->create(
            HabitItem::from($this->habitForm)
                ->withUserId(auth()->id())
        );

        $this->editingHabitId = 0;
        $this->showCreateDrawer = false;

        $this->toast(
            type: 'success',
            title: 'Habit added!',
        );
    }

    // → Persist updated category
    public function update(): void
    {
        $this->validate();

        try {
            $this->service->update(
                HabitItem::from($this->habitForm)
                    ->withId($this->editingHabitId)
                    ->withUserId(auth()->id())
            );
        } catch (Exception $exception) {
            $this->toast(
                type: 'error',
                title: 'Error updating',
                description: $exception->getMessage(),
            );
        }

        $this->editingHabitId = 0;
        $this->showEditDrawer = false;

        $this->toast(
            type: 'success',
            title: 'Category updated!',
        );
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
                      wire:click="create"
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
                                class="text-secondary mr-3"/>
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
                        <x-button
                            label="Edit"
                            class="btn-primary"
                            wire:click="edit({{ $habit->id }})"
                            spinner
                        />
                    </x-slot:actions>
                </x-card>
            </div>
        @endforeach
    </div>

    <x-drawer
        wire:model="showCreateDrawer"
        title="Create Habit"
        subtitle="What you want to achieve."
        separator
        with-close-button
        class="w-full lg:w-1/3"
    >
        <x-forms.habit-form model="habitForm" submit-action="store">

            <x-button label="Cancel"
                      @click="$wire.showCreateDrawer = false"/>
            <x-button type="submit"
                      label="Create"
                      icon="o-check"
                      class="btn-primary"/>

        </x-forms.habit-form>
    </x-drawer>

    <x-drawer
        wire:model="showEditDrawer"
        title="Edit Habit"
        separator
        right
        with-close-button
        class="w-full lg:w-1/3"
    >
        <x-forms.habit-form model="habitForm" submit-action="update">

            <x-button label="Cancel"
                      @click="$wire.showEditDrawer = false"/>
            <x-button type="submit"
                      label="Save"
                      icon="o-check"
                      class="btn-primary"/>

        </x-forms.habit-form>
    </x-drawer>

</div>
