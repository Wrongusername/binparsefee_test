<?php

use BinparseTest\BinDetails\BinDetailsDto;
use BinparseTest\BinDetails\BinDetailsProviderInterface;
use BinparseTest\CurrencyConverter\CurrencyConverter;
use BinparseTest\ExchangeRate\ExchangeRateDto;
use BinparseTest\ExchangeRate\ExchangeRateProviderInterface;
use BinparseTest\FeeAmountCalculator\FeeAmountCalculator;
use BinparseTest\FeeAmountCalculator\FeeAmountCalculatorFactory;
use BinparseTest\Input\InputProviderInterface;
use BinparseTest\Input\TransactionDTO;
use BinparseTest\Output\OutputInterface;
use BinparseTest\TransactionFeeParser;
use PHPUnit\Framework\TestCase;

class TransactionFeeParserTest extends TestCase
{
    private const EU_FEE_RATE = 0.01;
    private const WORLD_FEE_RATE = 0.02;
    const BASE_CURRENCY = 'EUR';

    private InputProviderInterface $inputProvider;
    private BinDetailsProviderInterface $binDetailsProvider;
    private ExchangeRateProviderInterface $exchangeRateProvider;
    private OutputInterface $outputInterface;
    private TransactionFeeParser $transactionFeeParser;


    public function setUp(): void
    {
        $this->inputProvider = $this->createMock(InputProviderInterface::class);
        $this->binDetailsProvider = $this->createMock(BinDetailsProviderInterface::class);
        $this->exchangeRateProvider = $this->createMock(ExchangeRateProviderInterface::class);
        $this->outputInterface = $this->createMock(OutputInterface::class);
        $this->transactionFeeParser = new TransactionFeeParser(
            baseCurrency: self::BASE_CURRENCY,
            inputProvider: $this->inputProvider,
            binProvider: $this->binDetailsProvider,
            exchangeRateProvider: $this->exchangeRateProvider,
            currencyConverter: new CurrencyConverter(),
            feeAmountCalculatorFactory: new FeeAmountCalculatorFactory(
                euFeeAmountCalculator:  new FeeAmountCalculator(self::EU_FEE_RATE),
                worldFeeAmountCalculator: new FeeAmountCalculator(self::WORLD_FEE_RATE)
            ),
            output: $this->outputInterface
        );
    }

    protected function referenceRound(float $amount): float
    {
        return ceil($amount * 100) / 100;
    }

    public function testSingleRowEufee()
    {
        $amount = 100;
        $rate = 1.09547;
        $fee = 0.01;
        $expected = $this->referenceRound(($amount / $rate) * $fee);

        $this->inputProvider->method('getInput')->willReturn(
            [
                new TransactionDTO('45717360', $amount, 'USD')
            ]
        );
        $this->binDetailsProvider
            ->method('getBinDetails')
            ->with('45717360')
            ->willReturn(
                new BinDetailsDto('DK')
            );
        $this->exchangeRateProvider
            ->method('getRates')
            ->willReturn(
                [
                    'EUR' => new ExchangeRateDto('EUR', 'EUR', 1),
                    'USD' => new ExchangeRateDto('EUR', 'USD', $rate)
                ]
            );

        $this->outputInterface->expects($this->exactly(1))
            ->method('writeLn')
            ->with($expected)
        ;

        $this->transactionFeeParser->process();
    }

    public function testRound()
    {
        $amount = 103.923;
        $expected = $this->referenceRound($amount);

        $this->assertEquals($this->transactionFeeParser->roundCeilCentUp($amount), $expected);
    }

    public function testFeeWorld()
    {
        $amount = 1000;
        $rate = 1.09547;
        $fee = 0.02;
        $expected = ($amount / $rate) * $fee;

        $transaction = new TransactionDTO('45417360', $amount, 'USD');
        $exchangeRate = new ExchangeRateDto('EUR', 'USD', $rate);
        $this->binDetailsProvider
            ->method('getBinDetails')
            ->with('45417360')
            ->willReturn(
                new BinDetailsDto('JP')
            );

        $this->assertEquals($this->transactionFeeParser->getTransactionFee($transaction, $exchangeRate), $expected);
    }
}