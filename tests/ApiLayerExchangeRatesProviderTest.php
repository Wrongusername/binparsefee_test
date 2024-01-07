<?php

use BinparseTest\Util\UnexpectedResponseFormatException;
use PHPUnit\Framework\TestCase;
use BinparseTest\HttpAccessor\HttpAccessorInterface;
use BinparseTest\ExchangeRate\ApilayerExchangeRatesProvider;
use BinparseTest\ExchangeRate\ExchangeRateDto;
use BinparseTest\ExchangeRate\MissingRateException;

class ApiLayerExchangeRatesProviderTest extends TestCase
{
    private readonly HttpAccessorInterface $httpAccessor;
    private readonly ApilayerExchangeRatesProvider $rateProvider;
    public function setUp(): void
    {
        $this->httpAccessor = $this->createMock(HttpAccessorInterface::class);
        $this->rateProvider = new ApilayerExchangeRatesProvider('abcd', $this->httpAccessor);
    }

    public function testSuccessResponse(): void
    {
        $responseJson = '{
          "base": "EUR",
          "rates": {
            "EUR": 1,
            "GBP": 0.86088,
            "JPY": 158.443308,
            "USD": 1.09547
          },
          "success": true
        }';
        $responseJsonContent = json_decode($responseJson, true);

        $this->httpAccessor->expects($this->exactly(1))
            ->method('fetch')
            ->willReturn($responseJson);
        $requestedRates = ['EUR', 'GBP', 'JPY', 'USD'];
        $res = $this->rateProvider->getRates('EUR', $requestedRates);

        $this->assertIsArray($res);
        $this->assertContainsOnlyInstancesOf(ExchangeRateDto::class, $res);
        $this->assertCount(count($requestedRates), $res);

        foreach ($requestedRates as $rate) {
            $this->assertArrayHasKey($rate, $res);
            $this->assertEquals($responseJsonContent['rates'][$rate], $res[$rate]->getRate());
        }
    }

    public function testUnexpectedResponseContent(): void
    {
        $this->httpAccessor->method('fetch')->willReturn('{}');
        $this->expectException(UnexpectedResponseFormatException::class);
        $this->rateProvider->getRates('EUR', ['USD', 'GBP']);
    }

    public function testJsonDecodeErrorException(): void
    {
        $this->httpAccessor->method('fetch')->willReturn('{
          "base": "EUR",
          "rates": {
            "EUR": 1,
            "GBP": 0.86088,
          },
          "success": true
        }');

        $requestedRates = ['EUR', 'GBP', 'JPY', 'USD'];
        $this->expectException(JsonException::class);
        $this->rateProvider->getRates('EUR', $requestedRates);
    }

    public function testMissingRateResponse(): void
    {
        $this->httpAccessor->method('fetch')->willReturn('{
          "base": "EUR",
          "rates": {
            "EUR": 1,
            "GBP": 0.86088
          },
          "success": true
        }');

        $requestedRates = ['EUR', 'GBP', 'JPY', 'USD'];
        $this->expectException(MissingRateException::class);
        $this->rateProvider->getRates('EUR', $requestedRates);
    }
}
