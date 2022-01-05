<?php

namespace App\Libs\HttpClient;

use Illuminate\Support\Facades\Log;

class HttpClient
{

    public static function getRequest($url, $method = 'GET', $data = []): string
    {
        $url = trim($url);

        try {
            $client = new \GuzzleHttp\Client();

            if ($method === 'POST') {
                $response = $client->post($url, [
                    \GuzzleHttp\RequestOptions::JSON => $data // or 'json' => [...]
                ]);
            } else {
                $response = $client->request('GET', $url);
            }

            $statusCode = $response->getStatusCode();

            return $response->getBody()->getContents();
        } catch (\Exception $exception) {
            $message = sprintf('%s url: %s', $exception->getMessage(), $url);
            Log::channel('single')->debug($message);
            return '';
        }
    }
}
