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

    #[ArrayShape(['price' => "float", 'title' => "mixed|string", 'description' => "mixed|string"])]
    public function getInfoProduct($contentPage): array
    {
        $begin = strpos($contentPage, '<div itemscope itemtype="http://schema.org/Product">');
        $contentViewItemscope = substr($contentPage, $begin,
            strpos($contentPage, '<div class="product-detail"') - $begin,
        );

        preg_match('/\<meta itemprop=\"name\" content=\"(.*)\"\>/m', $contentViewItemscope, $title);
        $title = count($title) === 2 ? $title[1] : '';

        preg_match('/\<meta itemprop=\"description\" content=\"(.*)" \/>/m', $contentViewItemscope, $description);
        $description = count($description) === 2 ? $description[1] : '';

        preg_match('/\<meta itemprop=\"price\" content=\"(.*)\"\>/m', $contentViewItemscope, $price);
        $price = count($price) === 2 ? $price[1] : 0;

        return [
            'price' => (float)($price),
            'title' => $title,
            'description' => $description,
        ];
    }

}
