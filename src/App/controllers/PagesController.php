<?php 
use DotzFramework\Core\Controller;
use DotzFramework\Utilities\FormGeneration;

class PagesController extends Controller{

	public function helloWorld($args = [], $id = null, $name = null){
		echo "Hello 2 World ";
		var_dump($args, $id, $name);
	}

	public function form(){
		$obj = new \stdClass();
		$obj->form = new FormGeneration();

		$this->view->load('form', $obj);
	}
        
}