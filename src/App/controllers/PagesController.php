<?php 
use DotzFramework\Core\Controller;
use DotzFramework\Modules\Form;

class PagesController extends Controller{

	public function customPage($arg1 = null, $arg2 = null, $arg3 = null){
		echo "This is a custom url dummy page.";
		var_dump($arg1, $arg2, $arg3);
	}

	public function form(){
		
		$user = [
			'name'=>'Mustafa', 
			'email'=>'mustafa@domain.com',
			'city'=>'mississauga', 
			'relocate'=>'relocate', 
			'gender'=>'male',
			'message'=>'I wish to join this project.'
		];

		$obj = new \stdClass();
		$obj->form = new Form();
		$obj->form->bind($user);

		$obj->data = [
			'cities' => [ 
				'oakville'=>'Oakville', 
				'brampton'=>'Brampton', 
				'milton'=>'Milton', 
				'burlington'=>'Burlington', 
				'mississauga'=>'Mississauga', 
				'toronto'=>'Toronto' 
			]
		];

		$this->view->load('form', $obj);
	}

	public function query( $id='1' ){
		
		$data = $this->query->execute(
			'SELECT * FROM example_table WHERE id = ?;', 
			[$id]
		);

		$this->view->load('query', $data[0]);
	}

	public function querytwo( $id='1' ){
		
		$data = $this->query->execute( 
			$this->query->fetchQuery('Example', 'get'), 
			[$id] 
		);

		$this->view->load('query', $data[0]);
	}

        
}