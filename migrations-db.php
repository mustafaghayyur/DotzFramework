<?php
// Used by Doctrine Migrations
require __DIR__ . '/vendor/autoload.php';

use DotzFramework\Core\Dotz;

$conf = Dotz::config('db');

return [
    'dbname' => $conf->name,
    'user' => $conf->user,
    'password' => $conf->password,
    'host' => $conf->host,
    'driver' => $conf->driverDoctrine
];