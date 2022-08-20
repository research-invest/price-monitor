<?php

namespace App\Markets;

use App\Markets\ozon\OzonRu;
use App\Markets\wb\WbRu;
use App\Models\Product;

class Markets
{
    const STATUS_ACTIVE = 1;

    protected array $requestData = [];
    protected $marketClass;
    private array $errors = [];
    private array $commands = [];
    private array $commandsBot = [
        'start',
//        'status' => 'Status',
//        'products' => 'Products list',
//        'report' => 'Report',
    ];

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->setRequestData($data);
            $this->getMarketClass();
        }
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
            if (!in_array($command, $this->commandsBot, true)) {
                $this->addError('Такой команды не существует\.');
                return null;
            }

            $this->commands = $this->command($command);
            return null;
        }

        $isUrl = filter_var($url, FILTER_VALIDATE_URL) === true;

        if ($command && !$isUrl) {
            //command
            return null;
        }

        return $this->getClassMarketByUrl($url);
    }

    protected function command(string $command): array
    {
        switch ($command) {
            case 'start' :
                return [
                    'text' => 'Салам пополам баля жи есь, давай ссылку с ягодОк пидорок',
                    'replyMarkup' => [
                        'keyboardButtonRows' => [
                            [
                                'кнопка 1 😄',
                                'кнопка 2 ❤️',
                                'кнопка 3 ❌',
                            ],
                            [
                                'кнопка 4 😡',
                                'кнопка 5 💄',
                                'кнопка 6 🧠',
                            ],
                        ],
                    ],
                ];
//            case 'status' :
//                return "i'm ok";
//            case 'products' :
//                $productPrices = Market::getProductPricesByUser();
//                return Market::getMassageForCommandProducts($command, $productPrices);
//            case 'report' :
//                $productPrices = Market::getProductPricesByUser();
//                return Market::getMassageForCommandReport($command, $productPrices);
            default :
                return [
                    'text' => "i'm ok"
                ];
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
            $this->addError('Не корректная ссылка, ну ты че баля, дай нормальную ссылку\!');
            return null;
        }

        if (substr_count($parseUrl['host'], WbRu::HOST)) {
            return new WbRu($url);
        } else if (substr_count($parseUrl['host'], OzonRu::HOST)) {
            return new OzonRu($url);
        } else {
            $this->addError('Принимаем ссылки на товары только с wildberries.ru и ozon.ru(пока не принимаем).');
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
