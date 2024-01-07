<?php

namespace BinparseTest\CurrencyConverter;

use BinparseTest\ExchangeRate\ExchangeRateDto;
use BinparseTest\Input\TransactionDTO;

interface CurrencyConverterInterface
{
    public function getConvertedRate(TransactionDTO $transaction, ExchangeRateDto $exchangeRate): float;
}
