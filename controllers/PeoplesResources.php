<?php 
use DotzFramework\Core\Controller;

class PeoplesResources extends Controller {

	public function get_resource($test=''){
		echo "Hello 1 World ".$test;
	}
        
        public function post_resource(){
		echo "Hello 2 World!!";
	}
        
        public function put_resource(){
		echo "Hello 3 World!!";
	}
        
        public function delete_resource(){
		echo "Hello 3 World!!";
	}
        
}