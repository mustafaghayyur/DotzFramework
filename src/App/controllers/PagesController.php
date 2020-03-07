<?php 
use DotzFramework\Core\Controller;
use DotzFramework\Modules\Form\Form;
use DotzFramework\Core\Dotz;

class PagesController extends Controller{

	/**
	 * Home page
	 */
	public function index($test=''){
		
		$data = [ 'msg' => 'Developed by Web Dotz' ];

		$this->view->load('home', $data);
	}

	/**
	 * 404 Error Page. Could be a json response, or a HTML view.
	 */
	public function notFound($uriArray){
		$this->view->sendToJson(['msg'=>'Page not found.', 'uri_array:'=>$uriArray]);
	}

	/**
	 * A custom url controller. 
	 * Shows the index GET value in both filtered & unfiltered forms.
	 * Go to the following URL in your browser:
	 * ://my-app-url/get?index=<script>var t='hello'; document.write(t);</script>
	 */
	public function showGetVars(){
		header('X-XSS-Protection: 0');
		$filtered = $this->input->secure()->get('index');
		$unfiltered = $this->input->get('index', false);

		$this->view->load('get', ['original'=>$unfiltered, 'filtered'=>$filtered]);
	}

	/**
	 * A custom url controller. 
	 * Shows the message POST value in both filtered & unfiltered forms:
	 */
	public function showPostVars(){

		$unfiltered = $this->input->post('message', false);

		$sanitized = $this->input->secure()->post('message');

		$validated = $this->input->secure()->post(
			'message', 
			FILTER_VALIDATE_REGEXP, 
			['options' => 
				[ 'regexp' => '/^M[a-z]{4}/' ]
			]
		);

		$this->view->load('post', ['original'=>$unfiltered, 'sanitized'=>$sanitized, 'validated'=>$validated]);
	}

	/**
	 * Shows several ways to query your database.
	 * Requires a successful run of migration 20200211144805.
	 */
	public function queries(){
		
		$data =[];

		$data['one'] = $this->query->execute(
			'SELECT * FROM test_table WHERE id = ?;', 
			[1]
		);

		$data['two'] = $this->query->execute( 
			$this->query->fetchQuery('Example', 'get'), 
			[2] 
		);

		$id = $this->query->quote('3');
		$data['three'] = $this->query->raw('SELECT * FROM test_table WHERE id = '.$id.';');

		$this->view->load('query', $data);
	}

	/**
	 * Shows a form generated with the Form module.
	 */
	public function form(){
		
		$systemData = [
			'name'=>'Mustafa', 
			'email'=>'mustafa@domain.com',
			'city'=>'mississauga', 
			'citizen'=>'citizen', 
			'gender'=>'male',
			'message'=>'Match this string'
			//'message'=>'<script>var t=\'I wish to join this project.\'; document.write(t);</script>'
		];

		// setup $obj to send to the view.
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