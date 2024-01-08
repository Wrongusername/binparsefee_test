<?php

namespace BinparseTest;

use BinparseTest\BinDetails\BinDetailsProviderInterface;
use BinparseTest\CurrencyConverter\CurrencyConverterInterface;
use BinparseTest\ExchangeRate\ExchangeRateDto;
use BinparseTest\ExchangeRate\ExchangeRateProviderInterface;
use BinparseTest\FeeAmountCalculator\FeeAmountCalculatorFactory;
use BinparseTest\Input\InputProviderInterface;
use BinparseTest\Input\TransactionDTO;
use BinparseTest\Output\OutputInterface;

/**
 * main module - get input transactions from file
 * prefetch exchange rates
 * output transaction fees depending on fetched binlist EU attribution
 */
readonly class TransactionFeeParser
{
    public function __construct(
        private string $baseCurrency,
        private InputProviderInterface $inputProvider,
        private BinDetailsProviderInterface $binProvider,
        private ExchangeRateProviderInterface $exchangeRateProvider,
        private CurrencyConverterInterface $currencyConverter,
        private FeeAmountCalculatorFactory $feeAmountCalculatorFactory,
        private OutputInterface $output
    ){}

    public function process(): void
    {
        $transactions = $this->inputProvider->getInput();
        $transactionCurrencies = $this->getUniqueCurrencies($transactions);
        $exchangeRates = $this->exchangeRateProvider->getRates($this->baseCurrency, $transactionCurrencies);

        foreach ($transactions as $transaction) {
            $fee = $this->getTransactionFee($transaction, $exchangeRates[$transaction->getCurrency()]);
            $this->output->writeLn($this->roundCeilCentUp($fee));
        }
    }

    /**
     * @param TransactionDTO[] $inputTransactions
     *
     * @return string[]
     */
    public function getUniqueCurrencies(array $inputTransactions): array
    {
        $uniqueCurrencies = [];

        foreach ($inputTransactions as $transaction) {
            $uniqueCurrencies[$transaction->getCurrency()] = true;
        }

        return array_keys($uniqueCurrencies);
    }

    public function roundCeilCentUp(float $amount): float
    {
        return ceil($amount * 100) / 100;
    }

    public function getTransactionFee(TransactionDTO $transaction, ExchangeRateDto $exchangeRate): float
    {
        $eurAmount = $this->currencyConverter->getConvertedRate($transaction, $exchangeRate);
        $binDetails = $this->binProvider->getBinDetails($transaction->getBin());
        $feeCalculator = $this->feeAmountCalculatorFactory->getCalculatorForTransaction($binDetails);

        return $feeCalculator->getFee($eurAmount);
    }
}
