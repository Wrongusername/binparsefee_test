<?php

namespace BinparseTest\Input;

readonly class TransactionDTO
{
    public function __construct(
        private string $bin,
        private float $amount,
        private string $currency
    ) {}

    public function getBin(): string
    {
        return $this->bin;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
