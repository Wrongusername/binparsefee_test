<?php

namespace BinparseTest\BinDetails;

use BinparseTest\HttpAccessor\HttpAccessorInterface;
use BinparseTest\Util\UnexpectedResponseFormatException;

/**
 * binlist.net wrapper - api for retrieving credit card issuer info by iin/bin (6-8 digits)
 * we're interested in country of origin / EU attribution
 */
readonly class BinlistProvider implements BinDetailsProviderInterface
{
    private const ENDPOINT_URL = 'https://lookup.binlist.net/%s';

    public function __construct(private HttpAccessorInterface $accessor)
    {}

    public function getBinDetails(string $binString): BinDetailsDto
    {
        $content = $this->accessor->fetch('GET', sprintf(self::ENDPOINT_URL, $binString));

        $json = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        if (empty($json) || !isset($json['country']['alpha2'])) {
            throw new UnexpectedResponseFormatException('Bad/unexpected response contents : ' . $content) ;
        }

        if ('' === ($json['country']['alpha2']))
        {
            throw new UnidentifiedBinException() ;
        }

        return new BinDetailsDto($json['country']['alpha2']);
    }
}
