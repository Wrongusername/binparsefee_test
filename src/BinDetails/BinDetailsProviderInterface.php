<?php

namespace BinparseTest\BinDetails;

interface BinDetailsProviderInterface
{
    public function getBinDetails(string $binString): BinDetailsDto;
}
