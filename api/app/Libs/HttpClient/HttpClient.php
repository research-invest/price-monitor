<?php

namespace App\Libs\HttpClient;

use GuzzleHttp\Client;

class HttpClient
{
    private Client $client;

    public function __construct($baseUrl = null, $options = [])
    {
        $options = array_merge([
            'timeout' => 30,
            'base_uri' => $baseUrl,
            'http_errors' => false,
        ], $options);

        $this->client = new Client(array_filter($options));
    }

    /**
     * @param $uri
     * @param array $options
     * @param string $method
     * @return \Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getBody(string $uri, array $options = [], string $method = 'GET'): \Psr\Http\Message\StreamInterface
    {
        $response = $this->client->request($method, $uri, [
            'headers'  =>  [
                'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
                'accept-encoding' => 'gzip, deflate, br',
                'accept-language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
                'cache-control' => 'no-cache',
                'dnt' => '1',
                'pragma' => 'no-cache',
                'sec-fetch-mode' => 'navigate',
                'sec-fetch-site' => 'none',
                'sec-fetch-user' => '?1',
                'upgrade-insecure-requests' => '1',
                'Cookie' => true,
                'user-agent' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36'
            ]
        ]);

        return $response->getBody();
    }

    /**
     * @param string $uri
     * @param array $options
     * @param string $method
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getContents(string $uri, array $options = [], string $method = 'GET'): string
    {
        return $this->getBody($uri,$options, $method)->getContents();
    }
}
