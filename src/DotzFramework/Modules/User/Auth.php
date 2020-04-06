<?php
namespace DotzFramework\Modules\User;

use DotzFramework\Core\Dotz;
use \Firebase\JWT\JWT;
use DotzFramework\Modules\User\Setup;

class Auth {

	public $method;

	public $message;

	/**
	 * $authMethod = 'session'|'token'
	 */
	public function __construct($authMethod = null){
		$this->method = empty($authMethod) ? Dotz::config('user.authMethod') : $authMethod;
	}

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
	
	

	public function register(Array $user){

		try {
	
			self::validateEmail($user['email']);
			self::validateUsername($user['username']);
			self::validatePassword($user['password']);

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

	public static function validateEmail($email){
		if(is_string($email) && !empty($email)){
			if(strlen($email) < 121){
				if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
					return true;
				}else{
					throw new \Exception('Email not formatted correctly.');
				}
			}else{
				throw new \Exception('Email value too large. Max 120 chars allowed.');
			}
		}else{
			throw new \Exception('Email value must be a string and not empty.');
		}

		return false;
	}

	public static function validateUsername($username){
		if(is_string($username) && !empty($username)){
			if(strlen($username) < 121){
				return true;
			}else{
				throw new \Exception('Username value too large. Max 120 chars allowed.');
			}
		}else{
			throw new \Exception('Username must be a string and not empty.');
		}

		return false;
	}

	public static function validatePassword($password){
		if(is_string($password) && !empty($password)){
			if(strlen($password) > 8 && strlen($password) < 101){
				return true;
			}else{
				throw new \Exception('Password must be between 8 and 100 characters in length.');
			}
		}else{
			throw new \Exception('Password must be a string and not empty.');
		}

		return false;
	}

	/**
	 * Authorizes the request as legitimate. Redirects  to login 
	 * if the session is not valid.
	 */
	public static function check($block = true){
		
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
		
		if($block === true){
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


}