<?php

namespace App\Channels;

use App\Libs\HttpClient\HttpClient;
use Illuminate\Notifications\Notification;

class TgChannel
{

    /**
     * @param \App\Models\ProductPrice $notifiable
     * @param Notification $notification
     * @return bool
     */
    public function send($notifiable, Notification $notification)
    {
        $httpClient = new HttpClient();

        foreach ($notification->getSubscribers() as $subscriber) {

            $notificationText = $notification->getText($subscriber);

            $content = $httpClient->getContents(env('TG_SERVICE_URL'), [
                \GuzzleHttp\RequestOptions::JSON => [
                    'chat_id' => (int)$subscriber['telegram_id'],
                    'text' => $notificationText,
                ]
            ], 'POST');

            if ($content) {
                $notifyData = $notification->getNotifyData();

                $notifyLog = new \App\Models\Notification();
                $notifyLog->setRawAttributes(
                    [
                        'subscriber_id' => $subscriber['subscriber_id'],
                        'product_id' => $notifyData['product_id'],
                        'price' => $notifyData['new_price'],
                        'notification' => $notificationText,
                    ]);
                $notifyLog->save();
            }
        }

        return true;
    }
}
