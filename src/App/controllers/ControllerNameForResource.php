<?php 
use DotzFramework\Core\Controller;

class ControllerNameForResource extends Controller {

	public function getResource($test=''){
		echo "Hello 1 World ".$test;
	}
        
        public function postResource(){
		echo "Hello 2 World!!";
	}
        
        public function putResource(){
		echo "Hello 3 World!!";
	}
        
        public function deleteResource(){
		echo "Hello 3 World!!";
	}
        
}