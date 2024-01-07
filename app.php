<?php

use BinparseTest\TransactionFeeParser;
use BinparseTest\Input\FileAccessorInterface;
use BinparseTest\Input\FileAccessor;
use BinparseTest\Input\InputProviderInterface;
use BinparseTest\Input\JsonFileInputProvider;
use BinparseTest\ExchangeRate\ExchangeRateProviderInterface;
use BinparseTest\ExchangeRate\ApilayerExchangeRatesProvider;
use BinparseTest\BinDetails\BinDetailsProviderInterface;
use BinparseTest\HttpAccessor\HttpAccessorInterface;
use BinparseTest\HttpAccessor\GuzzleHttpAccessor;
use BinparseTest\BinDetails\BinlistProvider;
use BinparseTest\CurrencyConverter\CurrencyConverterInterface;
use BinparseTest\CurrencyConverter\CurrencyConverter;
use BinparseTest\FeeAmountCalculator\FeeAmountCalculatorFactory;
use BinparseTest\Output\OutputInterface;
use BinparseTest\Output\ConsoleOutputProvider;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Client as GuzzleClient;
use BinparseTest\FeeAmountCalculator\FeeAmountCalculator;
use DI\ContainerBuilder;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();

$dotenv->required([
    'EXCHANGE_RATE_APILAYER_KEY',
])->notEmpty();

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(
    'config.php',
    [
        FileAccessorInterface::class =>
            DI\autowire(FileAccessor::class)->method(
                'setPath',
                path: __DIR__ . '/' . $argv[1]
            ),
        InputProviderInterface::class =>
            DI\autowire(JsonFileInputProvider::class),
        ExchangeRateProviderInterface::class =>
            DI\autowire(ApilayerExchangeRatesProvider::class)->constructor(
                apiKey: DI\env('EXCHANGE_RATE_APILAYER_KEY')
            ),
        GuzzleClientInterface::class =>
            DI\autowire(GuzzleClient::class),
        HttpAccessorInterface::class =>
            DI\autowire(GuzzleHttpAccessor::class),
        BinDetailsProviderInterface::class =>
            DI\autowire(BinlistProvider::class),
        OutputInterface::class =>
            DI\autowire(ConsoleOutputProvider::class),
        CurrencyConverterInterface::class =>
            DI\autowire(CurrencyConverter::class),
        FeeAmountCalculatorFactory::class =>
            DI\create()->constructor(
                euFeeAmountCalculator: DI\create(FeeAmountCalculator::class)->constructor(
                    rate: DI\get('eu.fee')
                ),
                worldFeeAmountCalculator: DI\create(FeeAmountCalculator::class)->constructor(
                    rate: DI\get('noneu.fee')
                )
            ),
        TransactionFeeParser::class =>
            DI\autowire()->constructor(
                baseCurrency: DI\get('base_currency')
            ),
    ]
);
$container = $containerBuilder->build();

$binParser = $container->get(TransactionFeeParser::class);
$binParser->process();