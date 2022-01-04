<?php

namespace App\Console\Commands;

use Illuminate\Console\Command as LCommand;
use \Illuminate\Support\Facades\Log;

class Command extends LCommand
{

    protected $name = 'test';

    protected function getRequest($url): string
    {
        $url = trim($url);

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $url);

            $statusCode = $response->getStatusCode();

            return $response->getBody()->getContents();
        } catch (\Exception $exception) {
            $message = sprintf('%s url: %s', $exception->getMessage(), $url);
            Log::channel('single')->debug($message);
            return '';
        }
    }
}
