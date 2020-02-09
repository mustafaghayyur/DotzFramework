<?php 
use DotzFramework\Core\Controller;

class PagesController extends Controller{

	public function index($args = [], $id = null, $name = null){
		echo "Hello 2 World ";
		var_dump($args, $id, $name);
	}
        
}