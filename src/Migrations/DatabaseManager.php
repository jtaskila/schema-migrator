<?php

declare(strict_types=1);

namespace Jtaskila\SchemaMigrator\Migrations;

use Jtaskila\SchemaMigrator\Database\Connection;

class DatabaseManager 
{
    private Connection $connection;

    public function __construct(
        Connection $connection 
    ) {
        $this->connection = $connection;
    }

    public function databaseExists(string $databaseName): bool 
    {
        $result = $this->connection->query(
            \sprintf(
                "SHOW DATABASES LIKE '%s'",
                $databaseName
            )
        )->fetch();

        if (!empty($result)) {
            return true;
        }

        return false;
    }

    public function createDatabase(string $databaseName): void 
    {
        $this->connection->exec(
            \sprintf(
                "CREATE DATABASE IF NOT EXISTS %s",
                $databaseName
            )
        );
    }

    public function useDatabase(string $databaseName): void 
    {
        $this->connection->exec(\sprintf(
            "USE %s",
            $databaseName
        ));
    }
}