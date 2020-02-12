<?php

use DotzFramework\Core\Controller;

class DefaultController extends Controller{

	public function index($test=''){
		$data = [ 'name' => 'Malaika Ghayyur', 'Phone' => '647-989-5389' ];

		$this->view->load('home', null, $data);
	}

	public function notFound($uriArray){
		echo "Page not found:";
		var_dump($uriArray);
	}
        
}