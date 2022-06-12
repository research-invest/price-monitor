<?php

namespace App\Http\Controllers;

use App\Http\Requests\TgService\MessageRequest;
use App\Markets\Markets;
use App\Models\MessageLog;
use App\Models\ProductSubscriber;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class TgServiceController extends Controller
{
    public function message(MessageRequest $request)
    {
        $data = $request->validated();

        $subscriber = Subscriber::firstOrCreate(
            ['telegram_id' => $data['chat_id']],
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'username' => $data['username'],
                'status' => Subscriber::STATUS_ACTIVE,
            ]
        );

        MessageLog::create([
                'subscriber_id' => $subscriber->id,
                'message' => $data['text_message'],
            ]
        );

        $markets = new Markets($data);

        if ($commands = $markets->getIsCommands()) {
            $massage = '';

            if ( $result = json_decode($commands[0]) ) {
                foreach ($result as $val) {
                    $title = $val->title ?? '';
                    $price = $val->price ?? '';
                    $url = $val->url ?? '';

                    $massage .= "{$title}: {$price}\r\n{$url}\r\n\r\n";
                }
            } else {
                $massage = join(', ', $commands);
            }

            return $massage;
        } elseif ($product = $markets->getProduct()) {
            ProductSubscriber::setProductSubscriber($subscriber->id, $product->id);
        } elseif ($errors = $markets->getErrors()) {
            return join(', ', $errors);
        }

        return 'Ваша ссылка успешно добавлена в систему.';
    }
}
