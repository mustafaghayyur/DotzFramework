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
	public function __construct($method = null){
		$this->method = empty($method) ? Dotz::config('user.authMethod') : $method;
	}

	/**
	 * Checks the supplied username/password combo against the database
	 */
	public function login($username, $password, $accessLevel = 3, $accessValidator = null){
		
		try{
	
			$q = Dotz::get()->load('query');
			$r = $q->execute('SELECT * FROM user WHERE username = ?', [$username]);

		}catch(\Exception $e){

			preg_match('#^\[[^\]]*\] - (.*)#', $e->getMessage(), $m);
			$this->message = $m[1];
			return false;

		}

		if(is_array($r) && count($r) > 0){

			if($accessValidator === null || !is_object($accessValidator)){
				$accessValidator = new AccessValidator();
			}

			if(!$accessValidator->check($r[0]['access_level'], $accessLevel)){
				$this->message = 'Your account does not have the correct access level. Request denied.';
				return false;
			}
			
			$hash = $r[0]['password'];

			if(true === password_verify($password, $hash)){
				$this->{'_'.$this->method.'Generate'}($r[0]);
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

			if(isset($user['accessLevel'])){
				$user['accessLevel'] = (int)$user['accessLevel'];
			}else{
				$user['accessLevel'] = 3;
			}

		} catch (\Exception $e) {

			$this->message = $e->getMessage();
			return false;
		
		}

		$passwordHash = password_hash($user['password'], PASSWORD_BCRYPT);

		try{
	
			$q = Dotz::get()->load('query');
			$n = $q->execute(
				'INSERT INTO user (email, username, password, access_level) VALUES (?, ?, ?, ?);', 
				[$user['email'], $user['username'], $passwordHash, $user['accessLevel']]
			);
			$id = (int)$q->pdo->lastInsertId();

		}catch(\Exception $e){

			preg_match('#^\[[^\]]*\] - (.*)#', $e->getMessage(), $m);
			$this->message = (isset($m[1]) && !empty($m[1])) ? $m[1] : $e->getMessage();
			return false;

		}

		if($n == 1 && is_int($id) && $id > 0){
			$this->message = 'Registration successful.';
			return true;
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
	public static function check($method = null, $redirect = true, $accessLevel = 3, $accessValidator = null){
		
		$c = Dotz::config('app');
		$u = Dotz::config('user');
		$method = ($method === null) ? $u->authMethod : $method;

		if($u === null){
			// User module has not been activated yet. Setup...
			Setup::install();
		}

		if($accessValidator === null || !is_object($accessValidator)){
			$accessValidator = new AccessValidator();
		}

		if($method === 'session'){
			
			$s = Dotz::get()->load('session');
			$s->start();

			if(!$accessValidator->check($s->get('accessLevel'), $accessLevel)){
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

		if($method === 'token'){
			
			$payload = self::_getTokenPayload($u->secretKey);
			
			if(!is_object($payload)){
				
				Dotz::get()->load('view')->json([
					'status' => 'error',
					'message' => $payload // $payload now carries an error message
				]);
				
				return false; 
			}

			if(!$accessValidator->check($payload->accessLevel, $accessLevel)){
				throw new \Exception('Your account does not have the correct access level. Request denied.');
			}

			Dotz::get()->load('view')->json([
				'status' => 'success',
				'message' => 'ok'
			]);
		}
	}

	/**
	 * Since JWT tokens cannot be destroyed
	 * and would expire within the timeout time...
	 *
	 * logout() only applies to session based authentication
	 */
	public function logout(){
		
		$c = Dotz::config('app');
		$u = Dotz::config('user');

		if($this->method === 'session'){
			
			$session = Dotz::get()->load('session');
			$session->start();
			$session->invalidate();
			return true;
		
		}

		if($this->method === 'token'){
			
			$payload = self::_getTokenPayload($u->secretKey);

			if(!is_object($payload)){
				$this->message = $payload; // $payload now carries an error message
				return 'error'; 
			}

			if(isset($payload->exp)){

				$timeRemaining = (int)$payload->exp - (int)time();

				if($timeRemaining < 0){
					$this->message = 'User Access Token has expired already.';
					return 'notice'; 
				}else{
					
					$timeRemaining = round(($timeRemaining / 60), 2);
					
					$this->message = 'User Access Token will expire in '.$timeRemaining.' minutes from now.';
					
					return 'notice'; 
				}
				
			}else{
				$this->message = 'User Access Token cannot expire.';
				return 'notice'; 
			}
		}

	}

	/**
	 * Helper function decode User Access Token
	 */
	protected static function _getTokenPayload($secretKey){

		$auth = Dotz::get()->load('input')->header('authorization');

		if($auth === null || $auth === false){
			return 'Could not retrieve HTTP Authorization Header.';
		}

		preg_match('#(Bearer )?(.*)#', $auth, $token);

		if(empty($token[2])){
			return 'User Access Token missing. Request failed.';
		}
		
		try{

			return JWT::decode($token[2], $secretKey, array('HS256'));

		}catch(\Exception $e){

			return 'Could not accept supplied User Access Token. Request failed. [JWT Error Message: '. $e->getMessage() .']';

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
		$session->set('accessLevel', $user['access_level']);

		$this->message = 'Login successful.';

	}

	/**
	 * Helper function used by login()
	 */
	protected function _tokenGenerate($user){
		
		$u = Dotz::config('user');

		$payload = array(
		    "id" => $user['id'],
		    "user" => $user['username'],
		    "accessLevel" => $user['access_level'],
		    "iat" => time()
		);

		if((int)$u->timeout > 0) {
			$payload['exp'] = (int)time() + (int)$u->timeout;
		}

		$this->message = JWT::encode($payload, $u->secretKey, 'HS256');

	}


}