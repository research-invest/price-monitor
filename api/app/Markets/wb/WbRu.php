<?php

namespace App\Markets\wb;

use App\Markets\Market;
use App\Models\Product;
use JetBrains\PhpStorm\ArrayShape;

class WbRu extends Market
{

    const MARKET_ID = 1;
    const HOST = 'wildberries.ru';

    public function getProduct(): ?Product
    {
        $parseUrl = parse_url($this->productUrl);

//        /catalog/17808915/detail.aspx

        preg_match('/\/catalog\/([0-9]+)\/detail\.aspx/', $parseUrl['path'], $externalId);
        $externalId = count($externalId) === 2 ? $externalId[1] : 0;

        if (!$externalId) {
            $this->addError('Не корректная ссылка на товар.');
            return null;
        }

        return $this->firstOrCreateProduct($externalId);
    }

    public function getInfoProduct($contentPage): array
    {
        $result = json_decode($contentPage);

        $salePriceU = $result->data->products[0]->salePriceU ?? '';
        $priceU = $result->data->products[0]->priceU ?? '';
        $price = $salePriceU ? $salePriceU : $priceU;
        $title = $result->data->products[0]->name ?? '';

        $priceFormat = number_format(substr($price,0,-2), 2, '.', '');

        return [
            'price' => $priceFormat,
            'title' => $title,
            //'description' => $description,
        ];
    }

    public function getExternalId(string $url): int
    {
        $parseUrl = parse_url($url);

        preg_match('/\/catalog\/([0-9]+)\/detail\.aspx/', $parseUrl['path'], $externalId);
        $externalId = count($externalId) === 2 ? (int)$externalId[1] : 0;

        return $externalId;
    }
}
