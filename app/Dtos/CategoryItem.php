<?php

declare(strict_types=1);

namespace App\Dtos;

use Spatie\LaravelData\Data;

final class CategoryItem extends Data
{
    public function __construct(
        public readonly int $id = 0,
        public readonly string $name = '',
        public readonly string $color = '#000000',
    ) {}

    public function withId(int $id): self
    {
        return new self(
            $id,
            $this->name,
            $this->color,
        );
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        unset($data['id']);

        return $data;
    }
}
