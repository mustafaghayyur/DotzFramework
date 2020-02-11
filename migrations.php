<?php
// Used by Doctrine Migrations

/**
 * Use the folloing bin file to run migrations on cli: 
 * ./vendor/bin/doctrine-migrations <command>
 */
 
require __DIR__ . '/vendor/autoload.php';

use DotzFramework\Core\Dotz;

$dotz = Dotz::get(__DIR__ . '/configs');
$conf = $dotz->container['configs']->props;

return [
    'name' => $conf->app->appName,
    'migrations_namespace' => $conf->migrations->nameSpace,
    'table_name' => $conf->migrations->tableName,
    'column_name' => $conf->migrations->colName,
    'column_length' => $conf->migrations->colLength,
    'executed_at_column_name' => $conf->migrations->executedAtColName,
    'migrations_directory' => $conf->app->appSystemPath .'/'. $conf->migrations->migrationsDirectory,
    'all_or_nothing' => $conf->migrations->allOrNothing,
    'check_database_platform' => $conf->migrations->checkDatabasePlatform
];