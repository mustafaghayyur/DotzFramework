<?php 

$m1 = memory_get_usage();
$t1 = microtime(true);

error_reporting(E_ALL & ~E_NOTICE);

require __DIR__ . '/vendor/autoload.php';

use DotzFramework\Core\Dotz;
use DotzFramework\Core\Router;

$dotz = Dotz::get();

try{
    
    $r = new Router($dotz->load('configs')->props);
    $r->do();

}catch (Exception $e){

    Dotz::get()->load('view')->json(["Exception:" => $e->getMessage()]);

}

$r->profiler('on', $m1, $t1);