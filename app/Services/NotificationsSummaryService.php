<?php

namespace App\Services;

use App\Libraries\Pushover\PushoverLibrary;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JsonException;

class NotificationsSummaryService
{
    public function execute(): void
    {
        $notifications = $this->getNotifications();
        if ($notifications->isEmpty()) {
            Log::warning('No notifications found');

            return;
        }

        PushoverLibrary::notify(
            $this->prepareNotification($notifications)
        );

        $this->markAsRead($notifications);
    }

    private function getNotifications(): Collection
    {
        return DB::table('notifications')
            ->where('type', User::class)
            ->where('read_at', null)
            ->oldest()
            ->get();
    }

    private function prepareNotification(Collection $notifications): string
    {
        $message = sprintf(
            "There were %d new %s Requests:\n",
            $notifications->count(),
            Str::of('Invitation')->plural($notifications->count()),
        );

        foreach ($notifications as $notification) {
            try {
                $data = json_decode((string) $notification->data, true, 512, JSON_THROW_ON_ERROR);
                if (empty($data['message'])) {
                    continue;
                }
            } catch (JsonException) {
                continue;
            }

            $message .= sprintf(
                "[%s] %s\n",
                Carbon::create($notification->created_at)?->format('M d, Y h:i A') ?? 'Unknown',
                $data['message'],
            );
        }

        return trim($message);
    }

    private function markAsRead(Collection $notifications): void
    {
        DB::table('notifications')
            ->whereIn('id', $notifications->pluck('id'))
            ->update([
                'read_at' => now(),
            ]);
    }
}
