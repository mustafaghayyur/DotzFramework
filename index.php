<?php 

$m1 = memory_get_usage();
$t1 = microtime(true);

require __DIR__ . '/vendor/autoload.php';

use DotzFramework\Core\Dotz;

//error_reporting(E_ALL & ~E_NOTICE);
$error = Dotz::module('error');
set_error_handler([$error, "handle"]);

try{

	// set timezone property in configs/app.txt
	date_default_timezone_set(Dotz::config('app.timezone'));
	
	// instantiate Router()
    $r = Dotz::module('router');

    // do the routing
    $r->do(); 
    
    // run profiler based on configuration setting
	$r->profiler(
		Dotz::config('app.profiler'),
		$m1, $t1
	);

}catch (Exception $e){

	$error->output($e);

}

$error->notices();
