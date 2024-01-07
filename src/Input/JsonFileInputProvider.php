<?php

namespace BinparseTest\Input;

use Exception;

/**
 * parse input json file and return DTOs array with input transactions
 */
readonly class JsonFileInputProvider implements InputProviderInterface
{
    public const EXPECTED_KEYS = ['bin' => true, 'amount' => true, 'currency' => true];
    public function __construct(private FileAccessorInterface $fileAccessor)
    {}

    /** {@inheritdoc} */
    public function getInput(): array
    {
        $data = $this->fileAccessor->getContents();
        $rows = explode("\n", $data);

        if (empty($rows)) {
            return [];
        }

        $output = [];

        foreach ($rows as $idx => $row) {
            if (empty($row)) {
                break;
            }

            $json = json_decode($row, true, 512, JSON_THROW_ON_ERROR);

            if (
                empty($json) ||
                count(array_intersect_key(self::EXPECTED_KEYS, $json)) < count(self::EXPECTED_KEYS)
            ) {
                throw new UnexpectedJsonInputRowException(
                    sprintf('input row %d unexpected: %s', $idx + 1, $row)
                );
            }

            $output[] = new TransactionDTO($json['bin'], $json['amount'], $json['currency']);
        }

        return $output;
    }
}
