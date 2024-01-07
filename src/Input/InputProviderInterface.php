<?php

namespace BinparseTest\Input;

interface InputProviderInterface
{
    /**
     * @return TransactionDTO[]
     */
    public function getInput(): array;
}
