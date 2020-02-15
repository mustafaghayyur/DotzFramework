<?php

use DotzFramework\Core\Controller;

class DefaultController extends Controller{

	public function index($test=''){
		
		$data = $this->model->query(
			'SELECT * FROM tweets WHERE id = ?;', 
			[2]
		);

		$this->view->load('home', null, $data[0]);
	}

	public function stored($test=''){
		
		$this->model->saveQuery(
			'first', 
			'SELECT * FROM tweets WHERE id = ?;'
		);

		$data = $this->model->query(
			'first', 
			[2]
		);

		$this->view->load('home', null, $data[0]);
	}

	public function notFound($uriArray){
		echo "Page not found:";
		var_dump($uriArray);
	}
        
}