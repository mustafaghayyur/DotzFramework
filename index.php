<?php 

$stMem = memory_get_usage();
$stTime = microtime(true);

error_reporting(E_ALL & ~E_NOTICE);

require __DIR__ . '/vendor/autoload.php';

use DotzFramework\Core\Dotz;
use DotzFramework\Core\Router;

$dotz = Dotz::get();

try{
    
    $r = new Router($dotz->load('configs')->props);
    $r->do();

}catch (Exception $e){

    Dotz::get()->load('view')->sendToJson(["Exception:" => $e->getMessage()]);

}

$enMem = memory_get_usage();
$enTime = microtime(true);

echo 'Time: ' . ($enTime - $stTime) ."<br/>";
echo 'Memory: ' . ( ($enMem - $stMem) / 1000) ."kb<br/>";

