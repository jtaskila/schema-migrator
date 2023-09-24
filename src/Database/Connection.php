<?php 

declare(strict_types=1);

namespace Jtaskila\SchemaMigrator\Database;

use Jtaskila\SchemaMigrator\Config\DatabaseConfig;
use PDO;
use PDOStatement;

class Connection 
{
    private ?PDO $connection = null;

    public function __construct(
        DatabaseConfig $config
    ) {
        $this->connection = new PDO(
            \sprintf('mysql:host=%s', $config->getHost()),
            $config->getUsername(),
            $config->getPassword(),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]
        );
    }

    public function close(): void 
    {
        $this->connection = null;
    }
    
    public function query(string $statement): PDOStatement
    {
        if (!$this->connection) {
            throw new \Exception('No database connection available');
        }

        return $this->connection->query($statement);
    }

    public function exec(string $statement): int 
    {
        if (!$this->connection) {
            throw new \Exception('No database connection available');
        }
        
        return $this->connection->exec($statement);
    }
}