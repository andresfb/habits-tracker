<?php

namespace App\Console\Dtos;

class TaskResultItem
{
    public function __construct(
        public bool $success,
        public string $message,
    ) {}
}
