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
		
		$this->view->load(
			'error', 
			[
				'msg' => 'Page not found', 
				'data' => 'path: '. $uriArray[0] .'/...'
			]
		);
	}

	/**
	 * A custom url controller. 
	 * Shows the index GET value in filtered & unfiltered forms.
	 */
	public function showGetVars($secure = ''){

		$packet['filtered'] = $this->input->get('index');

		if($secure == 'secure'){
			$packet['unfiltered'] = $this->input->verySecure()->get('index', false);
		}else{
			$packet['unfiltered'] = $this->input->secure()->get('index', false);
		}

		$this->view->load('get', $packet);
	}

	/**
	 * A custom url controller. 
	 * Shows the message POST value in filtered & unfiltered forms:
	 */
	public function showPostVars(){

		$packet = [];

		$packet['unfiltered'] = $this->input->post('message', false);
		$packet['sanitized'] = $this->input->secure()->post('message');
		$packet['validated'] = $this->input->secure()->post(
			'message', 
			FILTER_VALIDATE_REGEXP, 
			['options' => 
				[ 'regexp' => '/^H[a-z]{1}/' ]
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
			'name'=>'John Doe', 
			'email'=>'john@domain.com',
			'city'=>'mississauga', 
			'citizen'=>'citizen', 
			'gender'=>'male',
			'message'=>'Hi, I would like to join this project.'
		];

		$cities = [
			'oakville'=>'Oakville', 
			'brampton'=>'Brampton', 
			'milton'=>'Milton', 
			'burlington'=>'Burlington', 
			'mississauga'=>'Mississauga', 
			'toronto'=>'Toronto' 
		];

		// setup $packet to send to the view.
		$packet = [];
		
		$packet['cities'] = $cities;

		$packet['form'] = new Form();
		$packet['form']->bind($systemData);

		$this->view->load('form', $packet);
	}

	/**
	 * Shows how to retrieve a HTTP header. 
	 */
	public function header(){

		$auth = $this->input->header('authorization');
		preg_match('#(Bearer )?([a-zA-z0-9\.]*)#', $auth, $t);

		echo "Authorization token: " . $t[2];
	}

        
}