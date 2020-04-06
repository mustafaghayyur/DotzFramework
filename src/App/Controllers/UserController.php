<?php 

use DotzFramework\Core\Controller;
use DotzFramework\Core\Dotz;
use DotzFramework\Modules\User\Auth;
use DotzFramework\Modules\Form\Form;

/**
 * Shows off the User module.
 *
 * First time use will require following on-screen instructions to 
 * activate the module.
 */
class UserController extends Controller{
	
	/**
	 * Members' area page
	 */
	public function index(){
		
		Auth::check();

		$packet = [];
		$packet['msg'] = 'Logged in. | <a href="' .
							$this->configs->app->httpProtocol .
							'://'.$this->configs->app->url .
							'/user/logout">Logout</a>';

		$this->view->load('home', $packet);
	}

	/**
	 * Login page.
	 *
	 * Note the logic used for your own projects.
	 */
	public function login(){
		
		if(Auth::check('allow') === true){
			header('Location: ' .
						$this->configs->app->httpProtocol .
						'://' . $this->configs->app->url .
						'/' . $this->configs->user->loggedInUri);
			die();
		}

		// setup $packet to send to the view.
		$packet = [];

		if(!empty($this->input->post('submit', false))){
			
			$u = $this->input->secure()->post('username');
			$p = $this->input->secure()->post('password');

			$a = new Auth();

			if($a->login($u, $p)){
				
				header('Location: ' . $this->configs->app->httpProtocol . 
							'://' . $this->configs->app->url . 
							'/' . $this->configs->user->loggedInUri);
				die();
			
			}

			$packet['message'] = $a->message;

		}

		$packet['form'] = new Form();
		$this->view->load('login', $packet);
		
	}

	/**
	 * Logout page.
	 *
	 * Redirects to the login page upon success for session based
	 * authentication.
	 */
	public function logout(){

		Auth::logout();			
		
	}

	/**
	 * Registration page.
	 *
	 * Note the logic used for your own project development.
	 */
	public function signup(){

		if(Auth::check('allow') === true){
			header('Location: ' . $this->configs->app->httpProtocol .
						'://' . $this->configs->app->url . 
						'/' . $this->configs->user->loggedInUri);
			die();
		}
		
		// setup $packet to send to the view.
		$packet = [];
		
		if(!empty($this->input->post('submit', false))){
			
			$user = [
				'username' => $this->input->secure()->post('username'),
				'email' => $this->input->secure()->post('email'),
				'password' => $this->input->secure()->post('password')
			];
	
			$a = new Auth();
			
			if($a->register($user)){
				
				header('Location: ' . $this->configs->app->httpProtocol . 
							'://' . $this->configs->app->url . 
							'/' . $this->configs->user->loggedInUri);
				
				die();

			}

			$packet['message'] = $a->message;

		}

		$packet['form'] = new Form();
		$this->view->load('signup', $packet);
		
	}
        
}