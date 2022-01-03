<?php

namespace App\Http\Controllers;

use App\Http\Requests\TgService\MessageRequest;
use App\Models\MessageLog;
use App\Models\Product;
use App\Models\ProductSubscriber;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class TgServiceController extends Controller
{
    public function message(MessageRequest $request)
    {
//        $data = $request->all();
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

        $data = $request->validated();

        $product = new Product();
        $product->url = $data['text_message'];
        $product->save();

//        $subscriber->products()->attach($product);

        $productSubscriber = ProductSubscriber::firstOrCreate(
            ['subscriber_id' => $subscriber->id],
            ['product_id' => $product->id],
            [
                'subscriber_id' => $subscriber->id,
                'product_id' => $product->id,
            ]
        );


        return 'zbs';//CompanyResource::make($product)
    }
}
