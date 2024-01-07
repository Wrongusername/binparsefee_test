<?php
use PHPUnit\Framework\TestCase;
use BinparseTest\Input\FileAccessorInterface;
use BinparseTest\Input\JsonFileInputProvider;
use BinparseTest\Input\TransactionDTO;
use BinparseTest\Input\UnexpectedJsonInputRowException;

class JsonFileInputProviderTest extends TestCase
{
    private FileAccessorInterface $fileAccessor;
    private JsonFileInputProvider $jsonFileInputProvider;

    public function setUp(): void
    {
        $this->fileAccessor = $this->createMock(FileAccessorInterface::class);
        $this->jsonFileInputProvider = new JsonFileInputProvider($this->fileAccessor);
    }

    public function testSuccess(): void
    {
        $input = '{"bin":"45717360","amount":"100.00","currency":"EUR"}
{"bin":"516793","amount":"50.00","currency":"USD"}
{"bin":"45417360","amount":"10000.00","currency":"JPY"}
{"bin":"4745030","amount":"2000.00","currency":"GBP"}
{"bin":"41417360","amount":"130.00","currency":"USD"}';
        $inputRows = explode("\n", $input);

        $this->fileAccessor
            ->method('getContents')
            ->willReturn($input);

        $result = $this->jsonFileInputProvider->getInput();

        $this->assertIsArray($result);
        $this->assertContainsOnlyInstancesOf(TransactionDTO::class, $result);
        $this->assertCount(count($inputRows), $result);

        foreach ($inputRows as $idx => $row)
        {
            $inputData = json_decode($row, true);
            $this->assertEquals($inputData['bin'], $result[$idx]->getBin());
            $this->assertEquals($inputData['amount'], $result[$idx]->getAmount());
            $this->assertEquals($inputData['currency'], $result[$idx]->getCurrency());
        }
    }

    public function testBadJsonRowException(): void
    {
        $input = '{"bin":"45717360","amount":"100.00","currency":"EUR",}
{"bin":"516793","amount":"50.00","currency":"USD"}';
        $inputRows = explode("\n", $input);

        $this->fileAccessor
            ->method('getContents')
            ->willReturn($input);

        $this->expectException(JsonException::class);
        $this->jsonFileInputProvider->getInput();
    }

    public function testEmptyJsonRowException(): void
    {
        $input = '{}
{"bin":"516793","amount":"50.00","currency":"USD"}';

        $this->fileAccessor
            ->method('getContents')
            ->willReturn($input);

        $this->expectException(UnexpectedJsonInputRowException::class);
        $this->jsonFileInputProvider->getInput();
    }

    public function testMissingKeyJsonRowException(): void
    {
        $input = '{"bin":"45717360","amount":"100.00"}
{"bin":"516793","amount":"50.00","currency":"USD"}';

        $this->fileAccessor
            ->method('getContents')
            ->willReturn($input);

        $this->expectException(UnexpectedJsonInputRowException::class);
        $this->jsonFileInputProvider->getInput();
    }
}
