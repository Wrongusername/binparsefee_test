<?php

use BinparseTest\BinDetails\BinDetailsDto;
use PHPUnit\Framework\TestCase;
use BinparseTest\HttpAccessor\HttpAccessorInterface;
use BinparseTest\BinDetails\BinlistProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use BinparseTest\Util\UnexpectedResponseFormatException;
use BinparseTest\BinDetails\UnidentifiedBinException;

class BinlistProviderTest extends TestCase
{
    private readonly HttpAccessorInterface $httpAccessor;
    private readonly BinlistProvider $binlistProvider;
    public function setUp(): void
    {
        $this->httpAccessor = $this->createMock(HttpAccessorInterface::class);
        $this->binlistProvider = new BinlistProvider($this->httpAccessor);
    }

    public static function successProvider(): array
    {
        return [
            [
                '4745030',
                '{
                   "country":{
                      "alpha2":"LT"
                   }
                }',
                true
            ],
            [
                '45717360',
                '{
                   "country":{
                      "alpha2":"DK"
                   }
                }',
                true
            ],
            [
                '516793',
                '{
                   "country":{
                      "alpha2":"LT"
                   }
                }',
                true
            ],
            [
                '45417360',
                '{
                   "country":{
                      "alpha2":"JP"
                   }
                }',
                false
            ],
        ];
    }

    #[DataProvider('successProvider')]
    public function testSuccessResponse(string $bin, string $json, bool $isEu): void
    {
        $responseJsonContent = json_decode($json, true);

        $this->httpAccessor
            ->method('fetch')
            ->willReturn($json);

        $res = $this->binlistProvider->getBinDetails($bin);

        $this->assertInstanceOf(BinDetailsDto::class, $res);
        $this->assertEquals($responseJsonContent['country']['alpha2'], $res->getCountryCode());
        $this->assertEquals($isEu, $res->isEu());
    }

    public function testEmptyJson(): void
    {
        $this->httpAccessor
            ->method('fetch')
            ->willReturn('{}');

        $this->expectException(UnexpectedResponseFormatException::class);
        $this->binlistProvider->getBinDetails('12345');
    }

    public function testUnidentifiedBin(): void
    {
        $this->httpAccessor
            ->method('fetch')
            ->willReturn(                '{
                   "country":{
                      "alpha2":""
                   }
                }');

        $this->expectException(UnidentifiedBinException::class);
        $this->binlistProvider->getBinDetails('12345');
    }
}
