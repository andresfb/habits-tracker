<div>
    @props([
        'model',
        'submitAction',
    ])

    <x-form wire:submit.prevent="{{ $submitAction }}">
        <div class="grid gap-8 mb-5">
            <x-input label="Name" wire:model.defer="{{ $model }}.name" placeholder="Name" required inline />

            <x-textarea label="Description" wire:model.defer="{{ $model }}.description" placeholder="Description" rows="3" inline />

            <x-select label="Category" wire:model.defer="{{ $model }}.category_id" :options="$categories" icon="o-tag" required inline />

            <div class="grid grid-cols-2 gap-4">
                <x-input label="Target Value" wire:model.defer="{{ $model }}.target_value" type="number" required inline />

                <x-input label="Default Value" wire:model.defer="{{ $model }}.default_value" type="number" inline />

                <x-select label="Unit" wire:model.defer="{{ $model }}.unit_id" :options="$units" icon="o-adjustments-horizontal" required inline />

                <x-select label="Period" wire:model.defer="{{ $model }}.period_id" :options="$periods" icon="o-calendar-days" required inline />
            </div>

            <div class="mt-2">
                <x-toggle label="Multiple" wire:model.defer="{{ $model }}.allow_multiple_times" hint="Allow multiple entries per Period" />
            </div>

            <x-input
                label="Icon"
                wire:model.defer="{{ $model }}.icon"
                hint="Fin an icon on https://blade-ui-kit.com/blade-icons/"
                inline
            />

            <x-input label="Order" wire:model.defer="{{ $model }}.order_by" type="number" inline />
        </div>

        <x-slot name="actions">
            {{ $slot }}
        </x-slot>
    </x-form>
</div>
