<?php
namespace DotzFramework\Modules\User;

use DotzFramework\Core\Dotz;

/**
 * Authentication class for Session authentication
 */
class SessionAuth extends Auth {

	/**
	 * Login given user
	 *
	 * @param $username - string [username]
	 * @param $password - string [password]
	 * @param $level - int [access level required to pass check]
	 * @param $validator - obj [holds a access-validation class instance]
	 */
	public function login($username, $password, $level = 3, $validator = null){
		
		if($this->authenticateUser($username, $password, $level, $validator)){
			return $this->sessionGenerate($this->userRecord);
		}else{
			return false;
		}

	}

	/**
	 * Helper function used by login()
	 *
	 * @param $user - array [user array] 
	 */
	public function sessionGenerate($user){

		$session = Dotz::module('session');
		$session->start();
		$session->set('user', $user['username']);
		$session->set('signInTime', time());
		$session->set('lastActivity', time());
		$session->set('accessLevel', $user['access_level']);

		$this->message = 'Login successful.';

		return true;

	}

	/**
	 * Expires a user's sessions.
	 */
	public function logout(){
		
		$session = Dotz::module('session');
		$session->start();
		$session->invalidate();
		return true;		

	}

	/**
	 * Authorizes the request as legitimate. 
	 * 
	 * SessionAuth::check() should be called on every controller that
	 * needs user-authorization-validation.
	 * 
	 * Redirects to login page if the session is not valid. 
	 * 
	 * @param $redirect - bool [redirect user to login screen if check fails.]
	 * @param $level - int [access level required to pass check]
	 * @param $validator - obj [holds a access-validation class instance]
	 */
	public static function check($redirect = true, $level = 3, $validator = null){
		
		$c = Dotz::config('app');
		$u = Dotz::config('user');

		if($u === null){
			// User module has not been activated yet. Setup...
			Setup::install();
		}

		if($validator === null || !is_object($validator)){
			$validator = new ValidateAccess();
		}
			
		$s = Dotz::module('session');
		$s->start();

		if(!$validator->checkAccessLevel($s->get('accessLevel'), $level)){
			throw new \Exception('Your account does not have the correct access level. Request denied.');
		}
		
		if($s->get('lastActivity') !== null){
			
			$lastActivity = (int)time() - (int)$s->get('lastActivity');
			$to = ((int)$u->timeout == 0) ? 86400 : (int)$u->timeout;

			if($lastActivity < $to){
				$s->set('lastActivity', time());
				return true;
			}
		}

		// destroy the session...
		$s->invalidate();

		if($redirect === true){
			header('Location: '.$c->httpProtocol.'://'.$c->url.'/'.$u->loginUri);
			die();	
		}

	}

}