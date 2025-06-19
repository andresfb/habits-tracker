<?php

namespace App\Jobs;

use App\Services\NotificationSummaryService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotificationsSummaryJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @throws Exception
     */
    public function handle(NotificationSummaryService $service): void
    {
        try {
            $service->execute();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            throw $exception;
        }
    }
}
