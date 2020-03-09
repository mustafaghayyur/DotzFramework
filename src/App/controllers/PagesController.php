<?php 
use DotzFramework\Core\Controller;
use DotzFramework\Modules\Form\Form;
use DotzFramework\Utilities\CSRF;
use DotzFramework\Core\Dotz;

class PagesController extends Controller{

	/**
	 * Home page
	 */
	public function index($test=''){
		
		$packet = [ 'msg' => 'Developed by Web Dotz' ];

		$this->view->load('home', $packet);
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

		$packet['filtered'] = $this->input->get('index');
		$packet['unfiltered'] = $this->input->secure()->get('index', false);

		$this->view->load('get', $packet);
	}

	/**
	 * A custom url controller. 
	 * Shows the message POST value in both filtered & unfiltered forms:
	 */
	public function showPostVars(){

		$packet = [];

		$packet['unfiltered'] = $this->input->post('message', false);

		$packet['sanitized'] = $this->input->secure()->post('message');

		$packet['validated'] = $this->input->secure()->post(
			'message', 
			FILTER_VALIDATE_REGEXP, 
			['options' => 
				[ 'regexp' => '/^M[a-z]{4}/' ]
			]
		);

		$this->view->load('post', $packet);
	}

	/**
	 * Shows several ways to query your database.
	 * Requires a successful run of migration 20200211144805.
	 */
	public function queries(){
		
		$packet = [];

		$packet['one'] = $this->query->execute(
			'SELECT * FROM test_table WHERE id = ?;', 
			[1]
		);

		$packet['two'] = $this->query->execute( 
			$this->query->fetchQuery('Example', 'get'), 
			[2] 
		);

		$id = $this->query->quote('3');
		$packet['three'] = $this->query->raw('SELECT * FROM test_table WHERE id = '.$id.';');

		$this->view->load('query', $packet);
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
		];

		// setup $packet to send to the view.
		$packet = [];
		$packet['form'] = new Form();
		$packet['form']->bind($systemData);

		$packet['data'] = [
			'cities' => [ 
				'oakville'=>'Oakville', 
				'brampton'=>'Brampton', 
				'milton'=>'Milton', 
				'burlington'=>'Burlington', 
				'mississauga'=>'Mississauga', 
				'toronto'=>'Toronto' 
			]
		];

		$packet['jwt'] = CSRF::generateToken();

		$this->view->load('form', $packet);
	}

        
}