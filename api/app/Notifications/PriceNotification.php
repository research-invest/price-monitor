<?php

namespace App\Notifications;

use App\Models\ProductSubscriber;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PriceNotification extends Notification
{
    use Queueable;

    private array $notifyData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(array $notifyData)
    {
        $this->notifyData = $notifyData;
    }

    public function getNotifyData(): array
    {
        return $this->notifyData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['tg'];
    }

    public function getSubscribers(): array
    {
        return ProductSubscriber::query()
            ->select([
                's.id AS subscriber_id',
                's.telegram_id',
                's.first_name'
            ])
            ->join('subscribers AS s', 'product_subscribers.subscriber_id', '=', 's.id')
            ->where('product_subscribers.product_id', '=', $this->notifyData['product_id'])
            ->where('s.status', '=', Subscriber::STATUS_ACTIVE)
            ->orderBy('s.id', 'ASC')
            ->get()
            ->toArray();
    }

    /**
     * @param array $subscriber
     * @return string
     */
    public function getText($subscriber): string
    {
        return sprintf('Привет %s! цена на интересующий вас товар %s упала до %s (была: %s)',
            $subscriber['first_name'],
            $this->notifyData['product_url'],
            $this->notifyData['new_price'],
            $this->notifyData['old_price'],
        );
    }
}
