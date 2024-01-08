<?php

namespace BinparseTest\ExchangeRate;

use BinparseTest\HttpAccessor\HttpAccessorInterface;
use BinparseTest\Util\UnexpectedResponseFormatException;

/**
 * apilayer.com exchange rates api wrapper
 * requires personal api key and exlicit subscription to exhange rates api from dashboard
 * free access is 100 requests per month so prod usage would require caching
 */
readonly class ApilayerExchangeRatesProvider implements ExchangeRateProviderInterface
{
    private const ENDPOINT_URL = 'https://api.apilayer.com/exchangerates_data/latest';
    public function __construct(
        private string $apiKey,
        private HttpAccessorInterface $accessor
    ){}

    /**
     * {@inheritdoc}
     */
    public function getRates(string $rateFrom, array $ratesTo): array
    {
        $content = $this->accessor->fetch('GET', self::ENDPOINT_URL,
            [
                'query' => [
                    'base' => $rateFrom,
                    'symbols' => implode(',', $ratesTo)
                ],
                'headers' => [
                    'apikey' => $this->apiKey
                ]
        ]);

        $json = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        if (empty($json) || !isset($json['rates'])) {
            throw new UnexpectedResponseFormatException('Bad/unexpected response contents : ' . $content) ;
        }

        $rates = [];

        foreach ($ratesTo as $rate) {
            if (empty($json['rates'][$rate])) {
                throw new MissingRateException('One of requested rates not returned : ' . $rate) ;
            }

            $rates[$rate] = new ExchangeRateDto($rateFrom, $rate, $json['rates'][$rate]);
        }

        return $rates;
    }
}
