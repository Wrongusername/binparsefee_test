<?php

namespace BinparseTest\FeeAmountCalculator;

readonly class FeeAmountCalculator implements FeeAmountCalculatorInterface
{
    public function __construct(private float $rate)
    {}

    public function getFee(float $amount): float
    {
        return $amount * $this->rate;
    }
}
