<?php

namespace App\Markets\wb;

use App\Markets\Market;
use App\Models\Product;

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

}
