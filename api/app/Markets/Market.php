<?php

namespace App\Markets;

use App\Models\Product;

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
}
