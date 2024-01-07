<?php

namespace BinparseTest\Input;

interface FileAccessorInterface
{
    public function getContents(): string;
}
