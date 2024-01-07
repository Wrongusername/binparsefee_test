<?php

namespace BinparseTest\CurrencyConverter;

use BinparseTest\ExchangeRate\ExchangeRateDto;
use BinparseTest\Input\TransactionDTO;

class CurrencyConverter implements CurrencyConverterInterface
{
    public function getConvertedRate(TransactionDTO $transaction, ExchangeRateDto $exchangeRate): float
    {
        return $transaction->getAmount() / $exchangeRate->getRate();
    }
}
