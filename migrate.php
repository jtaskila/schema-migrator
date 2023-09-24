<?php

declare(strict_types=1);

use Jtaskila\SchemaMigrator\Config\DatabaseConfig;
use Jtaskila\SchemaMigrator\Config\MigratorConfig;
use Jtaskila\SchemaMigrator\Migrator;

require "vendor/autoload.php";

$databaseConfig = new DatabaseConfig();
$databaseConfig->setHost('127.0.0.1')
    ->setUsername('root')
    ->setDatabase('test_schema_migrator');

$migratorConfig = new MigratorConfig(__DIR__ . '/migrations');

$migrator = new Migrator($databaseConfig, $migratorConfig);
$migrator->execute();