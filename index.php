<?php 

$m1 = memory_get_usage();
$t1 = microtime(true);

//error_reporting(E_ALL & ~E_NOTICE);

require __DIR__ . '/vendor/autoload.php';

use DotzFramework\Core\Dotz;
use DotzFramework\Core\Router;

try{
	
	// grab system configs in $c
	$c = Dotz::get()->load('configs')->props;
    
	// instantiate Router()
    $r = new Router($c);
    
    // do the routing
    $r->do();
    
    // run profiler based on configuration setting
    $r->profiler($c->app->profiler, $m1, $t1);

}catch (Exception $e){

    Dotz::get()->load('view')->json(["Exception:" => $e->getMessage()]);

}