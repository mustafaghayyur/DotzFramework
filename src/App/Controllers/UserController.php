<?php 

use DotzFramework\Core\Controller;
use DotzFramework\Core\Dotz;
use DotzFramework\Modules\User\Auth;
use DotzFramework\Modules\Form\Form;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * This controller shows the power of FilterText and Quill.js.
 * With Quill.js and our filtering library called Filtext(),
 * you can create rich user input interfaces while keeping your
 * app safe from XSS vulnerabilities.
 */
class UserController extends Controller{
	
	public function index(){
		
		Auth::check();

		$packet = [];
		$packet['msg'] = 'Logged in. | <a href="'.$this->configs->app->httpProtocol.'://'.$this->configs->app->url.'/user/logout">Logout</a>
';

		$this->view->load('home', $packet);
	}

	public function login(){
		
		if(Auth::check('allow') === true){
			header('Location: '.$this->configs->app->httpProtocol.'://'.$this->configs->app->url.'/'.$this->configs->user->loggedInUri);
		}

		// setup $packet to send to the view.
		$packet = [];

		if(!empty($this->input->post('submit', false))){
			
			$u = $this->input->secure()->post('username');
			$p = $this->input->secure()->post('password');

			$a = new Auth();

			if($a->login($u, $p)){
				
				header('Location: '.$this->configs->app->httpProtocol.'://'.$this->configs->app->url.'/'.$this->configs->user->loggedInUri);
				die();
			
			}

			$packet['message'] = $a->message;

		}

		$packet['form'] = new Form();
		$this->view->load('login', $packet);
		
	}

	public function logout(){

		Auth::logout();			
		
	}

	public function signup(){

		if(Auth::check('allow') === true){
			header('Location: '.$this->configs->app->httpProtocol.'://'.$this->configs->app->url.'/'.$this->configs->user->loggedInUri);
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
				
				header('Location: '.$this->configs->app->httpProtocol.'://'.$this->configs->app->url.'/'.$this->configs->user->loggedInUri);
				
				die();

			}

			$packet['message'] = $a->message;

		}

		$packet['form'] = new Form();
		$this->view->load('signup', $packet);
		
	}

        
}