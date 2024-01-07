<?php

namespace BinparseTest\Output;

class ConsoleOutputProvider implements OutputInterface
{
    public function writeLn(string $msg)
    {
        echo $msg . "\n";
    }
}
