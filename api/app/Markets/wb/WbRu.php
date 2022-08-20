<?php

namespace App\Markets\wb;

use App\Libs\HttpClient\HttpClient;
use App\Markets\Market;
use App\Models\Product;
use JetBrains\PhpStorm\ArrayShape;

class WbRu extends Market
{

    const MARKET_ID = 1;
    const HOST = 'wildberries.ru';

    private HttpClient $httpClient;

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

    public function getProductPageData($productUrl): array
    {
        $this->httpClient = new HttpClient();

        $externalId = $this->getExternalId($productUrl);

        $uri = "https://card.wb.ru/cards/detail?spp=0&regions=68,64,83,4,38,80,33,70,82,86,75,30,69,22,66,31,48,1,40,71&stores=117673,122258,122259,125238,125239,125240,6159,507,3158,117501,120602,120762,6158,121709,124731,159402,2737,130744,117986,1733,686,132043&pricemarginCoeff=1.0&reg=0&appType=1&emp=0&locale=ru&lang=ru&curr=rub&couponsGeo=12,3,18,15,21&dest=-1029256,-102269,-1278703,-1255563&nm={$externalId}";

        $content = $this->httpClient->getContents($uri);

        $data = $this->getInfoProduct($content);

        return [
            'price' => $data['price'] ?? 0,
            'title' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
        ];
    }
}
