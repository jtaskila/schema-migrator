# Schema Migrator

Schema migrator is a simple 0-dependency utility for handling database migrations in a PHP application. It allows you to write the migrations in plain SQL files so you will know exactly what you get.

**This library is still experimental. Usage in production is not recommended.**

## Table of Contents
1. [Installation](#1-installation)
2. [Configuration](#2-configuration)
3. [Usage](#3-usage)
4. [Errors](#4-errors)
5. [Injecting a custom output handler](#5-injecting-a-custom-output-handler)

## 1. Installation
Install the library with composer. There is no stable release available yet, so you need to use VCS repository. 
```
composer config repositories.repo-name vcs https://github.com/jtaskila/schema-migrator
composer require jtaskila/schema-migrator:dev-main
```

## 2. Configuration 
The migrator expects two configuration objects to be passed to the constructor. There is also one optional parameter for defining the output interface.
```
public function __construct(
    DatabaseConfig $databaseConfig,
    MigratorConfig $migratorConfig, 
    ?OutputInterface $output = null
)
```

### DatabaseConfig
This object holds the database credentials. Password is an optional property to allow usage in local development environments which may not have a database password.

### MigratorConfig
This object holds other migrator related configs such as the directory
where the migrations should be searched.

Constructor takes the migration directory as a parameter. Note that the path must be **absolute**.

### Example 
You can call the migrator from anywhere, but the simplest way to get started is to create a new file called ``migrate.php`` with the example content below.

```
<?php

declare(strict_types=1);

require "vendor/autoload.php";

use Jtaskila\SchemaMigrator\Config\DatabaseConfig;
use Jtaskila\SchemaMigrator\Config\MigratorConfig;
use Jtaskila\SchemaMigrator\Migrator;

$databaseConfig = new DatabaseConfig();
$databaseConfig->setHost('127.0.0.1')
    ->setUsername('dbuser')
    ->setDatabase('test_schema_migrator');

$migratorConfig = new MigratorConfig(__DIR__ . '/migrations');

$migrator = new Migrator($databaseConfig, $migratorConfig);
$migrator->execute();
```

## 3. Usage

### 3.1. Creating new migrations
1. Create a new directory called ``migrations`` to your project root.
2. Create a new file ``1_my_first_migration.sql`` to the ``migrations directory``.
3. Copy the example migration and paste it into the file:
```
CREATE TABLE my_table (
    id int NOT NULL AUTO_INCREMENT,
    my_column varchar(255),
    PRIMARY KEY(id)
);
```
4. Run the migrations. 
5. Check your database, there should be a new table called ``my_table`` with two columns: ``id`` and ``my_column``.

Migrations can be divided to subdirectories for clarity. You can create a directory ``migrations/my_table`` and put all the migrations affecting that specific table there.

### 3.2. Executing migrations
The ``migrate.php`` file created in the previous step can be executed from anywhere, but the recommended way is to run it from CLI.

```
php migrate.php
```

When the ``Migrator->execute`` method is called, the migrator creates a database connection, creates the configured database if it does not exists and then searches all the migration files from the defined directory. Then the SQL statements from the files are applied to the database.

Migrator saves the executed migrations to a special table in the database called ``migrations``. That way it can keep track of all the executed migrations and to prevent duplicate execution.

## 4. Errors

During the migration process, many kind of errors may happen. The errors are propagated as exceptions to the top level and are not handled in any way. This is intended behaviour so the migration won't ever fail silently.

## 5. Injecting a custom output handler
For example, if you want to write all messages from the Migrator to a log file, you can define a custom output handler and inject it as a dependency. The custom handler must implement the ``Jtaskila\SchemaMigrator\Api\OutputInterface``.

Example from ``Jtaskila\SchemaMigrator\Output\EchoCli``, which writes all messages to standard output.

```
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
```

After you have defined your own output handler class, you can inject it via the Migrator constructor:

```
$migrator = new Migrator(
    $databaseConfig, 
    $migratorConfig,
    new CustomOutputHandler()
);
```