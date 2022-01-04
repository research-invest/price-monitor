<?php

namespace App\Console\Commands;

use App\Helpers\MathHelper;
use App\Markets\Market;
use App\Markets\Markets;
use App\Models\Market as MarketModel;
use App\Models\Product;
use App\Models\ProductPrice;
use JetBrains\PhpStorm\ArrayShape;

/**
 * php artisan get-prices:run
 */
class GetPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get-prices:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $products = Product::query()
            ->select('products.*')
            ->join('markets AS m', 'products.market_id', '=', 'm.id')
            ->where('products.status', '=', Product::STATUS_ACTIVE)
            ->where('m.status', '=', MarketModel::STATUS_ACTIVE)
            ->orderBy('m.id', 'DESC')
            ->get();

        /**
         * @var Market $products
         */
        foreach ($products as $product) {

            $market = new Markets();

            $class = $market->getClassMarketByUrl($product->url);

            $prices = new ProductPrice();

            $data = $this->getProductPageData($product->url, $class);

            if ($data === false) {
                $product->status = Product::STATUS_DELETED;
                $product->save();

//                ChangeLog::create([
//                    'product_id' => $product->id,
//                    'log' => $product->id . 'Товар удален',
//                ]);

                continue;
            }

            if (!$product->title) {
                $product->title = $data['title'] ?? '';
                $product->description = $data['description'] ?? '';
                $product->save();
            }

            $lastPrice = ProductPrice::query()
                ->select('price')
                ->where('product_id', $product->id)
                ->orderBy('id', 'DESC')
                ->first();

            $price = $data['price'] ?? 0;

            $prices->setRawAttributes(
                [
                    'product_id' => $product->id,
                    'price' => $price,
                    'delta' =>
                        MathHelper::getPercentageChange($lastPrice ? $lastPrice->price : 0, $price),
                ]);

            $prices->save();
        }
    }

    #[ArrayShape(['price' => "int|mixed", 'title' => "mixed|string", 'description' => "mixed|string"])]
    protected function getProductPageData($productUrl, &$class): array
    {
        $content = $this->getRequest($productUrl);

        $data = $class->getInfoProduct($content);

        return [
            'price' => $data['price'] ?? 0,
            'title' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
        ];
    }
}
