<?php 
use DotzFramework\Core\Controller;
use DotzFramework\Modules\Form;

class PagesController extends Controller{

	public function customPage($args = [], $id = null, $name = null){
		echo "This is a custom url dummy page.";
		var_dump($args, $id, $name);
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
        
}