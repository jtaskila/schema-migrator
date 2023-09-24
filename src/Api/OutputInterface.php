<?php

declare(strict_types=1);

namespace Jtaskila\SchemaMigrator\Api;

interface OutputInterface
{
    public function writeLine(string $message): void;
}