<?php 

$m1 = memory_get_usage();
$t1 = microtime(true);

//error_reporting(E_ALL & ~E_NOTICE);

require __DIR__ . '/vendor/autoload.php';

use DotzFramework\Core\Dotz;
use DotzFramework\Core\Router;

try{
	    
	// instantiate Router()
    $r = new Router();
    
    // do the routing
    $r->do();
    
    // run profiler based on configuration setting
    $status = Dotz::get()->load('configs')->props->app->profiler;
	$r->profiler($status, $m1, $t1);

}catch (Exception $e){

    Dotz::get()->load('view')->json(["Exception:" => $e->getMessage()]);

}