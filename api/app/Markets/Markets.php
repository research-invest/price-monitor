<?php

namespace App\Markets;

use App\Markets\ozon\OzonRu;
use App\Markets\wb\WbRu;
use App\Models\Product;

class Markets
{
    protected array $requestData = [];
    protected ?Market $marketClass;
    private array $errors = [];

    public function __construct(){}

    public function setRequestData(array $data)
    {
        $this->requestData = $data;
    }

    public function getMarketClass()
    {
        $this->marketClass = $this->marketClass ?:$this->getClassMarket();

        if ($this->marketClass && ($errors = $this->marketClass->getErrors())) {
            $this->addErrors($errors);
        }
    }

    public function getProduct(): ?Product
    {
        $this->getMarketClass();

        return $this->marketClass?->getProduct();
    }

    protected function getClassMarket(): ?Market
    {
        $url = $this->requestData['text_message'];
        $command = $this->requestData['command'];

        $isUrl = filter_var($url, FILTER_VALIDATE_URL) === true;

        if ($command && !$isUrl) {
            //command
            return null;
        }

        return $this->getClassMarketByUrl($url);
    }

    public function getClassMarketByUrl(string $url): ?Market
    {
        $parseUrl = parse_url($url);

        if (substr_count($parseUrl['host'], WbRu::HOST)) {
            return new WbRu($url);
        } else if (substr_count($parseUrl['host'], OzonRu::HOST)) {
            return new OzonRu($url);
        } else {
            $this->addError('Ссылка не поддерживается');
        }

        return null;
    }

    protected function addError(string $error)
    {
        $this->errors[] = $error;
    }

    protected function addErrors(array $errors)
    {
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
