<?php 
use DotzFramework\Core\Controller;


class MyController extends Controller{

	public function myTestPage($test1=''){
		echo "Hello World: ".$test1;
                var_dump($test1);
	}
        
    public function myRealPage(){
		echo "Hello Worlsadasdasdd";
	}
}