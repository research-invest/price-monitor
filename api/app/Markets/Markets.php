<?php

namespace App\Markets;

use App\Markets\ozon\OzonRu;
use App\Markets\wb\WbRu;
use App\Models\Product;

class Markets
{
    protected array $requestData = [];
    protected $marketClass;
    private array $errors = [];
    private array $commands = [];
    private array $commandsBot = [
        'status' => 'Status',
        'products' => 'Products list',
        'report' => 'Report',
    ];

    public function __construct(array $data)
    {
        $this->setRequestData($data);
        $this->getMarketClass();
    }

    public function setRequestData(array $data)
    {
        $this->requestData = $data;
    }

    public function getMarketClass()
    {
        $this->marketClass = $this->marketClass ?: $this->getClassMarket();

        if ($this->marketClass && ($errors = $this->marketClass->getErrors())) {
            $this->addErrors($errors);
        }
    }

    public function getProduct(): ?Product
    {
        return $this->marketClass?->getProduct();
    }

    protected function getClassMarket()
    {
        $url = $this->requestData['text_message'];
        $command = $this->requestData['command'];

        if ($command) {
            if (!array_key_exists($command, $this->commandsBot)) {
                $this->addError('Такой команды не существует.');
                return null;
            }

            $this->commands[] = $this->command($command);
            return null;
        }

        $isUrl = filter_var($url, FILTER_VALIDATE_URL) === true;

        if ($command && !$isUrl) {
            //command
            return null;
        }

        return $this->getClassMarketByUrl($url);
    }

    protected function command(string $command): string
    {
        switch ($command) {
            case 'status' :
            default :
                return "i'm ok";
            case 'products' :
            case 'report' :
                return 'in progress';
        }

    }

    public function getIsCommands(): array
    {
        return $this->commands;
    }

    public function getClassMarketByUrl(string $url): ?Market
    {
        $parseUrl = parse_url($url);

        if (empty($parseUrl['host'])) {
            $this->addError('Не корректная ссылка');
            return null;
        }

        if (substr_count($parseUrl['host'], WbRu::HOST)) {
            return new WbRu($url);
        } else if (substr_count($parseUrl['host'], OzonRu::HOST)) {
            return new OzonRu($url);
        } else {
            $this->addError('Принимаем ссылки на товары только с wildberries.ru и ozon.ru.');
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
