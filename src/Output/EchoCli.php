<?php

declare(strict_types=1);

namespace Jtaskila\SchemaMigrator\Output;

use Jtaskila\SchemaMigrator\Api\OutputInterface;

class EchoCli implements OutputInterface
{
    public function writeLine(string $message): void
    {
        echo $message . "\n";
    }
}