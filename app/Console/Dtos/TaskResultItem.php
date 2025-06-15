<?php

declare(strict_types=1);

namespace App\Console\Dtos;

final class TaskResultItem
{
    public function __construct(
        public bool $success,
        public string $message,
    ) {}
}
