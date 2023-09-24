<?php

declare(strict_types=1);

namespace Jtaskila\SchemaMigrator\Migrations;

use Jtaskila\SchemaMigrator\Config\MigratorConfig;
use Jtaskila\SchemaMigrator\Database\Connection;

class MigrationRepository 
{
    private Connection $connection;
    private MigratorConfig $config;
    private FileFinder $fileFinder;
    private string $coreDirectory = __DIR__ . '/../core';

    public function __construct(
        Connection $connection, 
        MigratorConfig $config
    ) {
        $this->connection = $connection;  
        $this->config = $config;
        $this->fileFinder = new FileFinder();
    }

    /**
     * Get all available migrations 
     */
    public function getMigrations(): array 
    {
        $migrations = [];
        $migrations[] = $this->getCoreMigrations();
        $migrations[] = $this->getOtherMigrations();

        return $migrations;
    }

    /**
     * Get all core migrations which should be executed first
     */
    private function getCoreMigrations(): array 
    {
        $directory = $this->coreDirectory;
        $files = $this->fileFinder->findFiles($directory);

        $migrations = [];
        foreach ($files as $file) {
            $migrations[$file] = \file_get_contents(
                $directory . '/' . $file
            );
        }  

        return $migrations;
    }

    /**
     * Get all user defined migrations from the configured directory 
     */
    private function getOtherMigrations(): array 
    {
        $directory = $this->config->getDirectory();
        $files = $this->fileFinder->findFiles($directory);

        $migrations = [];
        foreach ($files as $file) {
            $migrations[$file] = \file_get_contents(
                $directory . '/' . $file
            );
        }  

        return $migrations;
    }

    /**
     * Execute a migration an save it to database 
     */
    public function executeMigration(string $name, string $migration): bool 
    {
        if (!$this->isExecuted($name)) {
            $this->connection->exec($migration);
            $this->saveMigration($name);
            
            return true;
        }

        return false;
    }

    /**
     * Check if a given migration is already executed 
     */
    private function isExecuted(string $name): bool  
    {
        if (!$this->migrationsTableExists() && $name === '1_create_migrations_table.sql') {
            return false;
        }

        $result = $this->connection->query(
            \sprintf(
                "SELECT path FROM migrations WHERE path = '%s'",
                $name 
            )
        )->fetchAll();

        return !empty($result);
    }
    
    /**
     * Save migration to database to prevent duplicate execution
     */
    private function saveMigration(string $name): void 
    {
        $this->connection->exec(
            \sprintf(
                "INSERT INTO migrations (path, timestamp) VALUES ('%s', '%s')",
                $name,
                time()
            )
        );
    }

    /**
     * Check if the migrations table exists
     */
    private function migrationsTableExists(): bool 
    {
        try {
            $this->connection->query("SELECT * FROM migrations");
        } catch (\Throwable) {
            return false;
        }

        return true;
    }
}