<?php

declare(strict_types=1);

namespace Jtaskila\SchemaMigrator\Config;

class MigratorConfig 
{
    private string $directory;

    public function __construct(
        string $directory 
    ) {
        $this->directory = $directory;  
    }

    public function getDirectory(): string 
    {
        return $this->directory;
    }

    public function validate(): bool 
    {
        if (!\file_exists($this->directory)) {
            return false;
        }

        return true;
    }
}