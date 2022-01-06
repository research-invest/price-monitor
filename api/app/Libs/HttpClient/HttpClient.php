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
        $response = $this->client->request($method, $uri, $options);
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
