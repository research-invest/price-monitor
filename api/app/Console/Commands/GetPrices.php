<?php

namespace App\Console\Commands;

use App\Helpers\MathHelper;
use App\Libs\HttpClient\HttpClient;
use App\Markets\Market;
use App\Markets\Markets;
use App\Models\Market as MarketModel;
use App\Models\Product;
use App\Models\ProductPrice;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\Table;

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

    private HttpClient $httpClient;

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

        $this->httpClient = new HttpClient();

        /**
         * @var Market $products
         */
        foreach ($products as $product) {

            $market = new Markets();

            $class = $market->getClassMarketByUrl($product->url);

            $prices = new ProductPrice();

            $data = $this->getProductPageData($product->url, $class);

            $this->info('Product['.$product->id.'] price:'. $data['price']);

            if (empty($data['price'])) {
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

            if ($lastPrice && ($data['price'] === $lastPrice->price)) {
                continue;
            }

            $prices->setRawAttributes(
                [
                    'product_id' => $product->id,
                    'price' => $data['price'],
                    'delta' => MathHelper::getPercentageChange($lastPrice ? $lastPrice->price : 0, $data['price']),
                ]);

            $prices->save();

            if ($lastPrice && $data['price'] < $lastPrice->price) {
                $notifyData = [
                    'new_price' => $data['price'],
                    'old_price' => $lastPrice->price,
                    'product_id' => $product->id,
                    'product_url' => $product->url,
                ];

                $prices->notify(new \App\Notifications\PriceNotification($notifyData));
            }
        }
    }

    #[ArrayShape(['price' => "int|mixed", 'title' => "mixed|string", 'description' => "mixed|string"])]
    protected function getProductPageData($productUrl, &$class): array
    {
        $content = $this->httpClient->getContents($productUrl);

        $data = $class->getInfoProduct($content);

        return [
            'price' => $data['price'] ?? 0,
            'title' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
        ];
    }


    private function tableTest()
    {
        // Create a new Table instance.
        $table = new Table($this->output);

        // Set the table headers.
        $table->setHeaders([
            'Site', 'Description'
        ]);

        // Set the contents of the table.
        $table->setRows([
            ['https://laravel.com',        'The official Laravel website'],
            ['https://forge.laravel.com/', 'Painless PHP Servers'],
            ['https://envoyer.io/',        'Zero Downtime PHP Deployment']
        ]);

        // Render the table to the output.
        $table->render();

    }

}
