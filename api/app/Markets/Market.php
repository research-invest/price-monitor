<?php

namespace App\Markets;

use App\Models\Product;
use App\Models\ProductPrice;

abstract class Market
{
    protected string $productUrl;
    private array $errors = [];

    public function __construct($url)
    {
        $this->productUrl = $url;

        $this->getProduct();
    }

    abstract public function getProduct();
    abstract public function getInfoProduct($contentPage): array;

    protected function addError($error)
    {
        $this->errors[] = $error;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    protected function firstOrCreateProduct(int $externalId)
    {
        return Product::firstOrCreate(
            [
                'market_id' => static::MARKET_ID,
                'external_id' => $externalId
            ],
            [
                'market_id' => static::MARKET_ID,
                'external_id' => $externalId,
                'url' => $this->productUrl,
            ]
        );
    }

    public static function getProductPricesByUser()
    {
        return ProductPrice::query()
            ->select([
                'product_prices.price',
                'product_prices.delta',
                'p.url',
                'p.title'
            ])
            ->forActive()
            ->get()
            ->toArray();
    }

    public static function getMassageForCommandReport(string $command, array $data) :string
    {
        $response = '';

        if (empty($data)) {
            return "no data";
        }

        foreach ($data as $key => $val) {
            $title = $val['title'] ?? '';
            $price = $val['price'] ?? '';
            $url = $val['url'] ?? '';
            $delta = (int)$val['delta'] ?? '';

            if ($delta > 0) {
                $prefix = "Цена выросла на {$delta}%";
            } elseif ($delta > 0) {
                $prefix = "Цена уменьшилась на {$delta}%";
            } else {
                $prefix = 'Цена не изменилась';
            }

            $response .= "{$title}: {$price} - {$prefix}\r\n{$url}\r\n\r\n";
        }

        return $response;
    }

    public static function getMassageForCommandProducts(string $command, array $data) :string
    {
        $response = '';

        if (empty($data)) {
            return "no data";
        }

        foreach ($data as $key => $val) {
            $title = $val['title'] ?? '';
            $price = $val['price'] ?? '';
            $url = $val['url'] ?? '';

            $response .= "{$title}: {$price}\r\n{$url}\r\n\r\n";
        }

        return $response;
    }
}
