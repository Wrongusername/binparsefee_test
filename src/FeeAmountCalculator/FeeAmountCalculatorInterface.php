<?php

namespace BinparseTest\FeeAmountCalculator;

interface FeeAmountCalculatorInterface
{
    public function getFee(float $amount): float;
}
