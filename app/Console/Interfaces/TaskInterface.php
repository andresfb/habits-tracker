<?php

namespace App\Console\Interfaces;

use App\Console\Dtos\TaskResultItem;

interface TaskInterface
{
    public function handle(): TaskResultItem;
}
