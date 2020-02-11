<?php 
error_reporting(E_ALL & ~E_NOTICE);

require __DIR__ . '/vendor/autoload.php';

use DotzFramework\Core\Dotz;
use DotzFramework\Core\Router;

$dotz = Dotz::get(__DIR__ . '/configs');

try{
    
    $r = new Router($dotz->container['configs']->props);
    $r->do();

}catch (Exception $e){

    echo "Exception: ". $e->getMessage().'<br/>';

}



