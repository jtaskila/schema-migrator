<?php

declare(strict_types=1);

namespace Jtaskila\SchemaMigrator\Output;

use Jtaskila\SchemaMigrator\Api\OutputInterface;

class EchoCli implements OutputInterface
{
    public function writeLine(string $message): void
    {
        echo date('[Y-m-d H:i:s] ') . $message . "\n";
    }
}