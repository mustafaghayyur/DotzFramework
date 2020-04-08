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
		SessionAuth::check();
		
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
		
		if(SessionAuth::check('allow') === true){
			// if user is logged in already ... redirect them
			header('Location: '.$this->url.'/'.$this->configs->user->loggedInUri);
			die();
		}

		// setup $packet to send to the view.
		$packet = [];

		if(!empty($this->input->post('submit', false))){
			// process form submission...
			$u = $this->input->secure()->post('username');
			$p = $this->input->secure()->post('password');
			$a = new SessionAuth();

			if($a->login($u, $p)){
				// redirect to memeber's area...
				header('Location: '.$this->url.'/'.$this->configs->user->loggedInUri);
				die();
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
			// SessionAuth::logout() returns a boolean true for session method...
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

		if(SessionAuth::check('allow') === true){
			// if the user is signed in, redirect to member's area...
			header('Location: '.$this->url.'/'.$this->configs->user->loggedInUri);
			die();
		}
		
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
				// registration successful. 
				// log user into system...
				if($a->login($user['username'], $user['password'])){
					header('Location: '.$this->url.'/'.$this->configs->user->loggedInUri);
					die();
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
	 * Token Based Auth Example:
	 * Check out:
	 *  - http://yourappurl/user/api/token
	 *  - http://yourappurl/user/api/exit
	 *  - http://yourappurl/user/api/register
	 *  - http://yourappurl/user/api/
	 *
	 * Requires Authorization http header:
	 * 	Authorization: Bearer <valid token>
	 * for successful passing of TokenAuth::check()
	 */
	public function api($path = null){

		// since the app.txt file has authMethod set to 'session';
		// you will need to pass in a custom $method value of 'token'
		// for each new TokenAuth() instance and TokenAuth::check() call.
		$a = new TokenAuth(); 

		switch($path){

			case 'token':

				$u = $this->input->post('username');
				$p = $this->input->post('password');
				
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

					$this->view->json(['status' => 'error', 'message' => 'Post data missing.']);

				}

			case 'exit':
				
				// TokenAuth::logout() returns a status string for token method...
				$status = $a->logout();

				$this->view->json([
					'status' => $status, 
					'message' => $a->message
				]);

			case 'register':

				if(!empty($this->input->post('username', false))){
					// data has been posted...
					$user = [
						'username' => $this->input->post('username'),
						'email' => $this->input->post('email'),
						'password' => $this->input->post('password')
					];
								
					if($a->register($user)){
						
						$this->view->json([
								'status' => 'success', 
								'message' => $a->message
							]);
					}

					$this->view->json(['status' => 'error', 'message' => $a->message]);

				}else{
					$this->view->json(['status' => 'error', 'message' => 'Post data missing.']);
				}

			default:
				// logged in page..
				TokenAuth::check();

				$this->view->json(['status' => 'success', 'message' => 'Logged in']);

		}
		
	}
        
}