<?php

namespace BinparseTest\ExchangeRate;

readonly class ExchangeRateDto
{
    public function __construct(
        // alpha3 currency code
        private string $rateFrom,
        // alpha3 currency code
        private string $rateTo,
        private float $rate
    ) {}

    public function getRateFrom(): string
    {
        return $this->rateFrom;
    }

    public function getRateTo(): string
    {
        return $this->rateTo;
    }

    public function getRate(): float
    {
        return $this->rate;
    }
}
