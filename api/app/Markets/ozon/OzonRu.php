<?php

namespace App\Markets\ozon;

use App\Markets\Market;
use App\Models\Product;

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



}
