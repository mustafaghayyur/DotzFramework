<?php

use DotzFramework\Core\Controller;

class DefaultController extends Controller{

	public function index($test=''){
		
		$data = $this->query->execute(
			'SELECT * FROM tweets WHERE id = ?;', 
			[2]
		);

		$this->view->load('/home', $data[0]);
	}

	public function stored($test=''){
		
		$data = $this->query->execute( $this->query->fetchQuery('Tweets', 'get'), [2] );

		$this->view->load('/home', $data[0]);
	}

	public function notFound($uriArray){
		echo "Page not found:";
		var_dump($uriArray);
	}
        
}