<?php

namespace App\Dtos;

use Spatie\LaravelData\Data;

class TrackerItem extends Data
{
    public function __construct(
        public readonly int    $id,
        public readonly string $name,
        public readonly string $icon,
        public readonly string $subTitle,
        public readonly bool   $allowMore,
        public readonly bool   $needsEntry,
        public readonly string $needsEntryClass,
    ) {}
}
