<?php

use DotzFramework\Core\Controller;

class TestController extends Controller{

	public function index($test=''){
		echo "Hello 2 World !!".$test;
	}

	public function notFound($uriArray){
		echo "Page not found:";
		var_dump($uriArray);
	}
        
}