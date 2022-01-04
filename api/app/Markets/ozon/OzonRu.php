<?php

namespace App\Markets\ozon;

use App\Markets\Market;
use App\Models\Product;
use JetBrains\PhpStorm\ArrayShape;

class OzonRu extends Market
{
    const MARKET_ID = 2;
    const HOST = 'ozon.ru';

    public function getProduct(): ?Product
    {
        $parseUrl = parse_url($this->productUrl);

//        /product/trimmer-dlya-borody-i-usov-philips-oneblade-qp2520-20-s-3-nasadkami-grebnyami-139163836/

        preg_match('/([0-9]+)\/$/', $parseUrl['path'], $externalId);
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
        $begin = strpos($contentPage, '<script data-n-head="true" type="application/ld+json">');
        $contentViewItemscope = substr($contentPage, $begin,
            strpos($contentPage, '</script></head><body><div data-server-rendered="true"') - $begin,
        );

        preg_match('/"name":"(.*)",/U', $contentViewItemscope, $title);
        $title = count($title) === 2 ? $title[1] : '';

        preg_match('/"description":"(.*)",/U', $contentViewItemscope, $description);
        $description = count($description) === 2 ? $description[1] : '';

        preg_match('/"price":"(.*)",/U', $contentViewItemscope, $price);
        $price = count($price) === 2 ? $price[1] : 0; //check копейки

        return [
            'price' => (float)($price),
            'title' => $title,
            'description' => $description,
        ];
    }


}
