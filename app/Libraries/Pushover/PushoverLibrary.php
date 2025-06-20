<?php

declare(strict_types=1);

namespace App\Libraries\Pushover;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Serhiy\Pushover\Api\Message\Message;
use Serhiy\Pushover\Api\Message\Notification;
use Serhiy\Pushover\Application;
use Serhiy\Pushover\Recipient;

final class PushoverLibrary
{
    public static function notify(string $text, string $title = ''): void
    {
        if ($text === '' || $text === '0') {
            return;
        }

        if ($title === '' || $title === '0') {
            $title = 'Habit Tracker new Invitations';
        }

        $application = new Application(Config::string('pushover.token'));
        $recipient = new Recipient(Config::string('pushover.user'));

        $message = new Message(nl2br($text.PHP_EOL), $title);
        $message->setIsHtml(true);

        $notification = new Notification($application, $recipient, $message);
        $response = $notification->push();

        if ($response->isSuccessful()) {
            return;
        }

        $errors = [
            "Found errors sending Pushover Notification: {$text}",
        ];

        $errors = implode("\n", array_merge($errors, $response->getErrors()));
        Log::error($errors);
    }
}
