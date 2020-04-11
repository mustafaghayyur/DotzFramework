<?php
// Used by Doctrine Migrations

/**
 * Doctrine Migrations Documentation:
 * https://www.doctrine-project.org/projects/doctrine-migrations/en/2.2/reference/introduction.html#introduction 
 * 
 * Use the following bin file to run migrations on cli: 
 * ./vendor/bin/doctrine-migrations <command>
 */
 
require __DIR__ . '/vendor/autoload.php';

use DotzFramework\Core\Dotz;

$conf = Dotz::module('configs')->props;

return [
    'name' => $conf->app->name,
    'migrations_namespace' => $conf->migrations->nameSpace,
    'table_name' => $conf->migrations->tableName,
    'column_name' => $conf->migrations->colName,
    'column_length' => $conf->migrations->colLength,
    'executed_at_column_name' => $conf->migrations->executedAtColName,
    'migrations_directory' => $conf->app->systemPath .'/'. $conf->migrations->migrationsDirectory,
    'all_or_nothing' => $conf->migrations->allOrNothing,
    'check_database_platform' => $conf->migrations->checkDatabasePlatform
];