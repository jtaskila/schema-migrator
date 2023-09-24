<?php

declare(strict_types=1);

namespace Jtaskila\SchemaMigrator;

use Jtaskila\SchemaMigrator\Api\OutputInterface;
use Jtaskila\SchemaMigrator\Config\DatabaseConfig;
use Jtaskila\SchemaMigrator\Config\MigratorConfig;
use Jtaskila\SchemaMigrator\Database\Connection;
use Jtaskila\SchemaMigrator\Migrations\DatabaseManager;
use Jtaskila\SchemaMigrator\Migrations\MigrationRepository;
use Jtaskila\SchemaMigrator\Output\EchoCli;

class Migrator
{
    private DatabaseConfig $databaseConfig;
    private OutputInterface $output;  
    
    private Connection $connection;
    private DatabaseManager $databaseManager;
    private MigrationRepository $migrationRepository;
    
    public function __construct(
        DatabaseConfig $databaseConfig,
        MigratorConfig $migratorConfig, 
        ?OutputInterface $output = null
    ) {
        $this->databaseConfig = $databaseConfig;
        if (!$this->databaseConfig->validate()) {
            throw new \Exception('Invalid database configuration');
        }

        $this->output = $output ?? new EchoCli();        
        $this->connection = new Connection($this->databaseConfig);
        $this->databaseManager = new DatabaseManager($this->connection);
        $this->migrationRepository = new MigrationRepository($this->connection, $migratorConfig);
    }

    /**
     * Executes database migrations 
     */
    public function execute(): void 
    {
        $this->output->writeLine(
            \sprintf("Migrating database '%s'...", $this->databaseConfig->getDatabase())
        );
        $this->createDatabase();
        $count = $this->runMigrations();

        if (!$count) {
           $this->output->writeLine('Nothing to migrate');

           return; 
        }

        $this->output->writeLine(\sprintf("Executed %s migration(s)", $count));
        $this->connection->close();
    }

    /**
     * Creates a database if it does not exist 
     */
    private function createDatabase(): void 
    {
        $databaseName = $this->databaseConfig->getDatabase();
        if ($this->databaseManager->databaseExists($databaseName)) {
            $this->databaseManager->useDatabase($databaseName);

            return;
        }

        $this->output->writeLine('Database does not exist, creating...');

        $this->databaseManager->createDatabase($databaseName);
        $this->databaseManager->useDatabase($databaseName);

        $this->output->writeLine(\sprintf("Database '%s' created", $databaseName));
    }

    /**
     * Run the individual database migrations
     * 
     * @return int The count of executed migrations 
     */
    private function runMigrations(): int  
    {
        $migrations = $this->migrationRepository->getMigrations();
        $migrationCount = 0;

        foreach ($migrations as $batch) {
            foreach ($batch as $name => $migration) {
                $result = $this->migrationRepository->executeMigration($name, $migration);

                if ($result) {
                    $this->output->writeLine(\sprintf(
                        "Running migration '%s'",
                        $name
                    ));
                    $migrationCount = $migrationCount + 1;
                }
            }
        }     
        
        return $migrationCount;
    }
}