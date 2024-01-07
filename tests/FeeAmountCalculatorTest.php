<?php

use BinparseTest\FeeAmountCalculator\FeeAmountCalculator;
use BinparseTest\FeeAmountCalculator\FeeAmountCalculatorInterface;
use PHPUnit\Framework\TestCase;

class FeeAmountCalculatorTest extends TestCase
{
    private const EU_RATE = 0.01;
    private const WORLD_RATE = 0.02;

    private FeeAmountCalculatorInterface $euFeeAmountCalc;
    private FeeAmountCalculatorInterface $worldFeeAmountCalc;

    public function setUp(): void
    {
        $this->euFeeAmountCalc = new FeeAmountCalculator(self::EU_RATE);
        $this->worldFeeAmountCalc = new FeeAmountCalculator(self::WORLD_RATE);
    }

    public function testWorld(): void
    {
        $amount = 1000;
        $this->assertEquals($this->worldFeeAmountCalc->getFee($amount), $amount * self::WORLD_RATE);
    }

    public function testEu(): void
    {
        $amount = 2000;
        $this->assertEquals($this->euFeeAmountCalc->getFee($amount), $amount * self::EU_RATE);
    }
}
