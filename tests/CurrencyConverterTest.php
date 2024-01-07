<?php

use BinparseTest\CurrencyConverter\CurrencyConverter;
use BinparseTest\CurrencyConverter\CurrencyConverterInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use BinparseTest\Input\TransactionDTO;
use BinparseTest\ExchangeRate\ExchangeRateDto;
use PHPUnit\Framework\TestCase;

class CurrencyConverterTest extends TestCase
{
    private CurrencyConverterInterface $currencyConverter;

    public function setUp(): void
    {
        $this->currencyConverter = new CurrencyConverter();
    }

    public static function inputProvider(): array
    {
        return [
            'EUR' => [100.00, 1],
            'USD' => [50.00, 1.09547],
            'JPY' => [10000.00, 158.443308],
            'GBP' => [2000.00, 0.86088]
        ];
    }


    #[DataProvider('inputProvider')]
    public function testConvert(float $amount, float $rate): void
    {
        $transaction = new TransactionDTO('1234', $amount, 'ABC');
        $exchangeRate = new ExchangeRateDto('ABC', 'ABC', $rate);

        $this->assertEquals(
            $this->currencyConverter->getConvertedRate($transaction, $exchangeRate), $amount / $rate
        ) ;
    }
}