<?php

namespace BinparseTest\Input;

use Exception;

class FileAccessor implements FileAccessorInterface
{
    private string $path;

    public function getContents(): string
    {
        if (!$this->path) {
            throw new \InvalidArgumentException('Source path not set');
        }

        if (!file_exists($this->path)) {
            throw new \InvalidArgumentException('Source file does not exist in workdir');
        }

        return file_get_contents($this->path);
    }

    public function setPath(string $path)
    {
        $this->path = $path;
    }
}
