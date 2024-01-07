<?php

namespace BinparseTest\BinDetails;

readonly class BinDetailsDto
{
    public const EU_ISO_LIST = [
        'AT',
        'BE',
        'BG',
        'CY',
        'CZ',
        'DE',
        'DK',
        'EE',
        'ES',
        'FI',
        'FR',
        'GR',
        'HR',
        'HU',
        'IE',
        'IT',
        'LT',
        'LU',
        'LV',
        'MT',
        'NL',
        'PO',
        'PT',
        'RO',
        'SE',
        'SI',
        'SK',
    ];

    private bool $isEu;

    public function __construct(private string $countryCode){
        $this->isEu = in_array($countryCode, self::EU_ISO_LIST, true);
    }

    public function isEu(): bool
    {
        return $this->isEu;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }
}
