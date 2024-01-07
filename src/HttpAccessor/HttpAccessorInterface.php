<?php

namespace BinparseTest\HttpAccessor;

interface HttpAccessorInterface
{
    public function fetch(string $method, string $url, array $params = []): string;
}
