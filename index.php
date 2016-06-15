<?php 

//set this to the directory path where router files are stored.
$routerDirectory = 'router';
        
require_once($routerDirectory.'/routingHandler.php');
use \router\RoutingHandler;

try{
    $router = new RoutingHandler($routerDirectory);
}catch (Exception $e){
    echo "Router Exception: ". $e->getMessage().'<br/>';
}



