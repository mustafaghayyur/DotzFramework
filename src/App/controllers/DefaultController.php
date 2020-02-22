<?php

use DotzFramework\Core\Controller;

class DefaultController extends Controller{

	public function index($test=''){
		
		$data = [ 'name' => 'Mustafa Ghayyur' ];

		$this->view->load('home', $data);
	}

	public function notFound($uriArray){
		$this->view->sendToJson(['msg'=>'Page not found.', 'uri_array:'=>$uriArray]);
	}
        
}