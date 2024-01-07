<?php

namespace BinparseTest\FeeAmountCalculator;

use BinparseTest\BinDetails\BinDetailsDto;

class FeeAmountCalculatorFactory
{
    public function __construct(
        private readonly FeeAmountCalculatorInterface $euFeeAmountCalculator,
        private readonly FeeAmountCalculatorInterface $worldFeeAmountCalculator
    ) {}

    public function getCalculatorForTransaction(BinDetailsDto $binDetailsDto): FeeAmountCalculatorInterface
    {
        if ($binDetailsDto->isEu()) {
            return $this->euFeeAmountCalculator;
        }

        return $this->worldFeeAmountCalculator;
    }
}

