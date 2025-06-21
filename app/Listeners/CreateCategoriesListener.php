<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\CreateBaseCategoriesAction;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;

final readonly class CreateCategoriesListener implements ShouldQueue
{
    public function __construct(private CreateBaseCategoriesAction $action) {}

    public function handle(Registered $event): void
    {
        $this->action->handle((int) $event->user->getAuthIdentifier());
    }
}
