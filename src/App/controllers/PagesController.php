<?php 
use DotzFramework\Core\Controller;
use DotzFramework\Modules\Form\Form;

class PagesController extends Controller{

	public function index($test=''){
		
		$data = [ 'name' => 'Mustafa Ghayyur' ];

		$this->view->load('home', $data);
	}

	public function notFound($uriArray){
		$this->view->sendToJson(['msg'=>'Page not found.', 'uri_array:'=>$uriArray]);
	}

	public function customPage($arg1 = null, $arg2 = null, $arg3 = null){
		echo "This is a custom url dummy page.";
		var_dump($arg1, $arg2, $arg3);
	}

	public function queryone( $id='1' ){
		
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

	public function rawquery( $id='1' ){
		
		$id = $this->query->quote($id);
		$data = $this->query->raw('SELECT * FROM example_table WHERE id = '.$id.';');

		$this->view->load('query', $data[0]);
	}

	public function form(){
		
		$systemData = [
			'name'=>'Mustafa', 
			'email'=>'mustafa@domain.com',
			'city'=>'mississauga', 
			'citizen'=>'citizen', 
			'gender'=>'male',
			'message'=>'I wish to join this project.'
		];

		$obj = new \stdClass();
		$obj->form = new Form();
		$obj->form->bind($systemData);

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