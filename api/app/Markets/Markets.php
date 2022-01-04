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

    public function __construct(array $data)
    {
        $this->requestData = $data;

        $this->marketClass = $this->getClassMarket();

        if ($this->marketClass && ($errors = $this->marketClass->getErrors())) {
            $this->addErrors($errors);
        }
    }

    public function getProduct(): ?Product
    {
        return $this->marketClass->getProduct();
    }

    protected function getClassMarket()
    {
        $url = $this->requestData['text_message'];
        $command = $this->requestData['command'];

        $isUrl = filter_var($url, FILTER_VALIDATE_URL) === true;

        if ($command && !$isUrl) {
            //command
            return;
        }

        $parseUrl = parse_url($url);

        if (substr_count($parseUrl['host'], WbRu::HOST)) {
            return new WbRu($url);
        } else if (substr_count($parseUrl['host'], OzonRu::HOST)) {
            return new OzonRu($url);
        } else {
            $this->addError('Ссылка не поддерживается');
        }
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
