<?php

namespace BinparseTest\HttpAccessor;

use GuzzleHttp\ClientInterface as GuzzleHttpClientInterface;

readonly class GuzzleHttpAccessor implements HttpAccessorInterface
{
    private const MIN_BAD_RESPONSE_CODE = 400;

    public function __construct(private GuzzleHttpClientInterface $client)
    {}

    public function fetch(string $method, string $url, array $params = []): string
    {
        $response = $this->client->request('GET', $url, $params);

        if ($response->getStatusCode() >= self::MIN_BAD_RESPONSE_CODE) {
            throw new \HttpException(
                'Failed to fetch url, response code : ' . $response->getStatusCode()
            );
        }

        return $response->getBody()->getContents();
    }
}
