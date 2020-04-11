<?php 

use DotzFramework\Core\Controller;
use DotzFramework\Core\Dotz;
use DotzFramework\Modules\User\SessionAuth;
use DotzFramework\Modules\User\TokenAuth;
use DotzFramework\Modules\Form\Form;

/**
 * Shows off the User module.
 *
 * First time use will require following on-screen instructions to 
 * activate the module.
 */
class UserController extends Controller{

	public $url;

	public function __construct(){
		parent::__construct();
		$this->url = $this->configs->app->httpProtocol .'://'. $this->configs->app->url;
	}
	
	/**
	 * Members' area page
	 */
	public function index(){
		
		SessionAuth::check(); //if not logged in ... user would get kicked out here.
		
		$packet = [];
		$packet['msg'] = 'Logged in. | <a href="'.$this->url.'/user/logout">Logout</a>';
		
		$this->view->load('home', $packet);
	}

	/**
	 * Login page.
	 *
	 * Note the logic used for your own projects.
	 */
	public function login(){
		
		$this->redirect();

		// setup $packet to send to the view.
		$packet = [];

		if(!empty($this->input->post('submit', false))){
			
			$a = new SessionAuth();

			// process form submission...
			$u = $this->input->secure()->post('username');
			$p = $this->input->secure()->post('password');

			if($a->login($u, $p)){
				$this->redirect();
			}

			// in case of an error,
			// The $message var in the view can be used to give feedback.
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
		
		$a = new SessionAuth();

		if($a->logout()){
			header('Location: '.$this->url.'/'.$this->configs->user->loginUri);
			die();
		}			
	}

	/**
	 * Registration page.
	 *
	 * Note the logic used for your own project development.
	 */
	public function signup(){

		$this->redirect();
		
		// setup $packet to send to the view.
		$packet = [];
		
		if(!empty($this->input->post('submit', false))){
			
			$user = [
				'username' => $this->input->secure()->post('username'),
				'email' => $this->input->secure()->post('email'),
				'password' => $this->input->secure()->post('password'),
				'accessLevel' => 3
			];
	
			$a = new SessionAuth();
			
			if($a->register($user)){
				
				// Registration successful. Log user into system...
				if($a->login($user['username'], $user['password'])){
					$this->redirect();
				}

			}

			// incase the process failed, $message in the view will hold
			// feedback for the user.
			$packet['message'] = $a->message;
		}

		$packet['form'] = new Form();
		$this->view->load('signup', $packet);
		
	}

	/**
	 * Redirect's a user to member's area
	 */
	public function redirect(){
		if(SessionAuth::check('allow') === true){
			// if user is logged in already ... redirect them
			header('Location: '.$this->url.'/'.$this->configs->user->loggedInUri);
			die();
		}
	} 



	/**
	 * Token Based Auth Example:
	 * Check out:
	 *  - http://yourappurl/user/api/
	 *  - http://yourappurl/user/api/token
	 *  - http://yourappurl/user/api/expiry
	 *  - http://yourappurl/user/api/register
	 *
	 * Requires HTTP header:
	 * 	Authorization: Bearer <valid token>
	 * for successful passing of TokenAuth::check()
	 */
	public function api($path = null){

		// since the app.txt file has authMethod set to 'session';
		// you will need to pass in a custom $method value of 'token'
		// for each new TokenAuth() instance and TokenAuth::check() call.
		$a = new TokenAuth(); 
		Dotz::module('router')->controllerUsed = 'dqwwResource';

		switch($path){

			case 'token':

				$u = $this->input->secure()->post('username');
				$p = $this->input->secure()->post('password');
				
				if(!empty($u)){
			
					if($a->login($u, $p)){
						
						$this->view->json([
							'status' => 'success',
							'token' => $a->message
						]);

					}else{
						$this->view->json([
							'status' => 'error',
							'message' => $a->message
						]);
					}

				}else{

					$this->view->json([
						'status' => 'error', 
						'message' => 'Post data missing.'
					]);

				}

			case 'expiry':
				
				$a->logout();

				$this->view->json([
					'status' => $a->status, 
					'message' => $a->message
				]);

			case 'register':

				if(!empty($this->input->post('username', false))){

					// data has been posted...process:
					$user = [
						'username' => $this->input->secure()->post('username'),
						'email' => $this->input->secure()->post('email'),
						'password' => $this->input->secure()->post('password')
					];
								
					if($a->register($user)){
						
						$this->view->json([
							'status' => 'success', 
							'message' => $a->message
						]);
					}

					$this->view->json([
						'status' => 'error', 
						'message' => $a->message
					]);

				}else{
					$this->view->json([
						'status' => 'error', 
						'message' => 'Post data missing.'
					]);
				}

			default:
				
				TokenAuth::check(); //if no valid token ... user would get kicked out here.

				// logged in page..
				$this->view->json([
					'status' => 'success', 
					'message' => 'Logged in'
				]);

		}
		
	}
        
}