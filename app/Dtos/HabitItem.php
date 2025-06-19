<?php

namespace App\Dtos;

use Spatie\LaravelData\Data;

class HabitItem extends Data
{
    public function __construct(
        public readonly int $id = 0,
        public readonly int $user_id = 0,
        public readonly int $category_id = 0,
        public readonly int $unit_id = 0,
        public readonly int $period_id = 0,
        public readonly string $name = '',
        public readonly string $description = '',
        public readonly string $icon = '',
        public readonly float $target_value = 0.00,
        public readonly float $default_value = 0.00,
        public readonly bool $allow_multiple_times = false,
        public readonly string $notes = '',
        public readonly int $order_by = 0,
    ) {}

    public function withId(int $id): self
    {
        return new self(
            id: $id,
            user_id: $this->user_id,
            category_id: $this->category_id,
            unit_id: $this->unit_id,
            period_id: $this->period_id,
            name: $this->name,
            description: $this->description,
            icon: $this->icon,
            target_value: $this->target_value,
            default_value: $this->default_value,
            allow_multiple_times: $this->allow_multiple_times,
            notes: $this->notes,
            order_by: $this->order_by,
        );
    }

    public function withUserId(int $userId): self
    {
        return new self(
            id: $this->id,
            user_id: $userId,
            category_id: $this->category_id,
            unit_id: $this->unit_id,
            period_id: $this->period_id,
            name: $this->name,
            description: $this->description,
            icon: $this->icon,
            target_value: $this->target_value,
            default_value: $this->default_value,
            allow_multiple_times: $this->allow_multiple_times,
            notes: $this->notes,
            order_by: $this->order_by,
        );
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        unset($data['id']);
            
        return $data;
    }
}
