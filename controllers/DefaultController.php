<?php

class DefaultController {

	public function myFirstPage($test=''){
		echo "Hello 2 World ".$test;
	}

	public function notFound($uriArray){
		echo "Page not found:";
		var_dump($uriArray);
	}
        
}