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

        $markets = new Markets();
        $markets->setRequestData($data);

//        $markets->getMarketClass();
//
//        if ($errors = $markets->getErrors()) {
//            return join(', ', $errors);
//        }

        $product = $markets->getProduct();

        if (!$product && ($errors = $markets->getErrors())) {
            return join(', ', $errors);
        }

//        $subscriber->products()->attach($product);

        ProductSubscriber::firstOrCreate(
            [
                'subscriber_id' => $subscriber->id,
                'product_id' => $product->id
            ],
            [
                'subscriber_id' => $subscriber->id,
                'product_id' => $product->id,
            ]
        );

        return 'Ваша ссылка успешно добавлена в систему.';
    }
}
