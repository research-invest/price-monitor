<?php

namespace App\Channels;

use App\Libs\HttpClient\HttpClient;
use Illuminate\Notifications\Notification;

class TgChannel
{
    public function send($notifiable, Notification $notification)
    {
        foreach ($notification->getSubscribers() as $subscriber) {
            HttpClient::getRequest(
                env('TG_SERVICE_URL'),
                'POST',
                [
                    'chat_id' => (int)$subscriber['telegram_id'],
                    'text' => $notification->getText($subscriber),
                ]
            );
        }
        return true;
    }
}
