<?php
// Used by Doctrine Migrations
require __DIR__ . '/vendor/autoload.php';

use DotzFramework\Core\Dotz;

$dotz = Dotz::get(__DIR__ . '/configs');
$conf = $dotz->load('configs')->props;

return [
    'dbname' => $conf->db->name,
    'user' => $conf->db->user,
    'password' => $conf->db->password,
    'host' => $conf->db->host,
    'driver' => $conf->db->driverDoctrine
];