<?php

namespace BinparseTest\FeeAmountCalculator;

use BinparseTest\BinDetails\BinDetailsDto;

/**
 * Calculates transaction fees depending on geographical CC issuer attribution
 * larger decision base would favour moving decision logic to calc classes supports() method and accepting array on constructor
 */
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

