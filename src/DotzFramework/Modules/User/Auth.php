<?php
namespace DotzFramework\Modules\User;

use DotzFramework\Core\Dotz;
use \Firebase\JWT\JWT;
use DotzFramework\Modules\User\Validate;
use DotzFramework\Modules\User\Setup;

class Auth {

	/**
	 * carries the authentication method
	 * ['session' OR 'token']
	 */
	public $method;

	/**
	 * carries a message to be available in the $auth->message
	 * property in the script you instantiate this class.
	 */
	public $message;

	/**
	 * The param $authMethod can have only one of two exact values:
	 * $authMethod = ['session'|'token']
	 */
	public function __construct($authMethod = null){
		$this->method = empty($authMethod) ? Dotz::config('user.authMethod') : $authMethod;
	}

	/**
	 * Checks the supplied username/password combo against the database
	 */
	public function login($username, $password){
		
		try{
	
			$q = Dotz::get()->load('query');
			$record = $q->execute('SELECT * FROM user WHERE username = ?', [$username]);

		}catch(\Exception $e){

			preg_match('#^\[[^\]]*\] - (.*)#', $e->getMessage(), $m);
			$this->message = $m[1];
			return false;

		}

		if(is_array($record) && count($record) > 0){

			$hash = $record[0]['password'];

			if(true === password_verify($password, $hash)){

				$this->{'_'.$this->method.'Generate'}($record[0]);
				return true;

			}else{
				$this->message = 'Password incorrect.';
			}

		}else{
			$this->message = 'Username not found.';
		}

		return false;

	}

	/**
	 * Registers a new user. Adds their username, password and email
	 * data to the user database table.
	 *
	 * To collect additional user data, create your own additional
	 * database tables and code to handle such data, seperately.
	 */
	public function register(Array $user, $validator = null){

		try {
	
			if($validator === null || !is_obj($validator)){
				$validator = new Validate();
			}

			$validator->email($user['email']);
			$validator->username($user['username']);
			$validator->password($user['password']);

		} catch (\Exception $e) {

			$this->message = $e->getMessage();
			return false;
		
		}

		$passwordHash = password_hash($user['password'], PASSWORD_BCRYPT);

		try{
	
			$q = Dotz::get()->load('query');
			$n = $q->execute(
				'INSERT INTO user (email, username, password) VALUES (?, ?, ?);', 
				[$user['email'], $user['username'], $passwordHash]
			);

			$id = (int)$q->pdo->lastInsertId();

		}catch(\Exception $e){

			preg_match('#^\[[^\]]*\] - (.*)#', $e->getMessage(), $m);
			$this->message = $m[1];
			return false;

		}

		if($n == 1 && is_int($id) && $id > 0){

			$this->message = 'Registration successful.';
			$u = Dotz::config('user');

			if($u->authMethod == 'session'){
				return $this->login($user['username'], $user['password']);
			}

			if($u->authMethod == 'token'){
				return true;
			}
		}

		$this->message = 'Registration of account failed.';
		return false;

	}

	/**
	 * Authorizes the request as legitimate. 
	 * 
	 * Redirects  to login if the session is not valid. 
	 * 
	 * Does not redirect the user (in session method) if the 
	 * $redirect argument is set to anything other than boolean true.
	 */
	public static function check($redirect = true){
		
		$c = Dotz::config('app');
		$u = Dotz::config('user');

		if($u === null){
			// User module has not been activated yet. Setup...
			Setup::install();
		}

		if($u->authMethod === 'session'){
			
			$s = Dotz::get()->load('session');
			$s->start();
			
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

		}

		if($u->authMethod === 'token'){

			$key = $c->secretKey;

			$auth = Dotz::get()->load('input')->header('authorization');
			preg_match('#(Bearer )?([a-zA-z0-9\.]*)#', $auth, $token);

			if(JWT::decode($token[2], $key, array('HS256'))){
				return true;
			}else{
				
				Dotz::get()->load('view')->json(
					[
						'status' => 'error',
						'message' => 'Could not authorize supplied token. Request failed.'
					]
				);
			}
		}
		
		if($redirect === true){
			header('Location: '.$c->httpProtocol.'://'.$c->url.'/'.$u->loginUri);
			die();	
		}
		
	}

	/**
	 * Since JWT tokens cannot be destroyed
	 * and would expire within the timeout time...
	 *
	 * logout() only applies to session based authentication
	 */
	public static function logout(){
		
		$c = Dotz::config('app');
		$u = Dotz::config('user');

		if($u->authMethod === 'session'){
			
			$session = Dotz::get()->load('session');
			$session->start();
			$session->invalidate();

			header('Location: '.$c->httpProtocol.'://'.$c->url.'/'.$u->loginUri);
			die();

		}

		if($u->authMethod === 'token'){
			
			if((int)$u->timeout > 0){

				$t = round((int)$u->timeout / 60);
				$msg = 'Token will expire in ~'.$t.' minutes from its creation time.';
				
			}else{
				$msg = 'Your token cannot expire.';
			}

			Dotz::get()->load('view')->json(
				[
					'status' => 'error',
					'message' => $msg
				]
			);
		}

	}

	/**
	 * Helper function used by login()
	 */
	protected function _sessionGenerate($user){

		$session = Dotz::get()->load('session');
		$session->start();
		$session->set('id', $user['id']);
		$session->set('user', $user['username']);
		$session->set('signInTime', time());
		$session->set('lastActivity', time());

		if(empty($this->message)){
			$this->message = 'Login successful.';
		}

	}

	/**
	 * Helper function used by login()
	 */
	protected function _tokenGenerate($user){
		$c = Dotz::config('user');

		$payload = array(
		    "id" => $user['id'],
		    "user" => $user['username'],
		    "iat" => time(),
		    "exp" => time() + ((int)$c->timeout)
		);

		$this->message = JWT::encode($payload, $c->secretKey, 'HS256');

	}


}