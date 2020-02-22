<?php

use DotzFramework\Core\Controller;

class DefaultController extends Controller{

	public function index($test=''){
		
		$data = [ 'name' => 'Mustafa Ghayyur' ];

		$this->view->load('/home', $data);
	}

	public function query($test=''){
		
		$data = $this->query->execute(
			'SELECT * FROM tweets WHERE id = ?;', 
			[2]
		);

		$this->view->load('/tweet', $data[0]);
	}

	public function querytwo($test=''){
		
		$data = $this->query->execute( 
			$this->query->fetchQuery('Tweets', 'get'), 
			[2] 
		);

		$this->view->load('/tweet', $data[0]);
	}

	public function notFound($uriArray){
		echo "Page not found:";
		var_dump($uriArray);
	}
        
}