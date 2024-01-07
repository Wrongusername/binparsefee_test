<?php

namespace BinparseTest\ExchangeRate;

interface ExchangeRateProviderInterface
{
    /**
     * @param string $rateFrom
     * @param string[] $ratesTo
     *
     * @return ExchangeRateDto[]
     */
    public function getRates(string $rateFrom, array $ratesTo): array;
}
