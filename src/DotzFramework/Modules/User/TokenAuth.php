<?php
namespace DotzFramework\Modules\User;

use DotzFramework\Core\Dotz;
use \Firebase\JWT\JWT;

/**
 * Authentication class for Token authentication
 */
class TokenAuth extends Auth{

	/**
	 * Stores a string status 
	 * helpful for returning a operation status.
	 */
	public $status;

	/**
	 * Login user an return user access token
	 *
	 * @param $username - string [username]
	 * @param $password - string [password]
	 * @param $level - int [access level required to pass check]
	 * @param $validator - obj [holds a access-validation class instance]
	 */
	public function login($username, $password, $level = 3, $validator = null){
		
		if($this->authenticateUser($username, $password, $level, $validator)){
			return $this->tokenGenerate($this->userRecord);
		}else{
			return false;
		}

	}

	/**
	 * Generates new token.
	 * 
	 * @param $user - array [user record]
	 */
	public function tokenGenerate($user){
		
		$u = Dotz::config('user');

		$payload = array(
		    "user" => $user['username'],
		    "accessLevel" => $user['access_level'],
		    "iat" => time()
		);

		if((int)$u->timeout > 0) {
			$payload['exp'] = (int)time() + (int)$u->timeout;
		}

		$this->message = JWT::encode($payload, $u->secretKey, 'HS256');
		return true;

	}

	/**
	 * Since JWT tokens cannot be destroyed
	 * and would expire within the timeout time...
	 *
	 * logout() provides user with expiry info
	 */
	public function logout(){
		
		$u = Dotz::config('user');

		$payload = self::getTokenPayload($u->secretKey);

		if(!is_object($payload)){
			
			$this->status =  'error'; 
			$this->message = $payload; // $payload now carries an error message
			return false;
		
		}

		if(isset($payload->exp)){

			$timeRemaining = (int)$payload->exp - (int)time();
			$timeRemaining = round(($timeRemaining / 60), 2);

			$this->status =  'notice'; 
			$this->message = 'User Access Token will expire in '.$timeRemaining.' minutes from now.';
			return true;
			
		}else{
			
			$this->status =  'notice'; 
			$this->message = 'User Access Token cannot expire.';
			return true;
		
		}

	}

	/**
	 * Authorizes the request as legitimate. 
	 *
	 * TokenAuth::check() should be called on every controller that
	 * needs user-authorization-validation.
	 * 
	 * Sends an error response on failed check. 
	 * 
	 * @param $level - int [access level required to pass check]
	 * @param $validator - obj [holds a access-validation class instance]
	 */
	public static function check($level = 3, $validator = null){
		
		$u = Dotz::config('user');

		if($u === null){
			// User module has not been activated yet. Setup...
			Setup::install();
		}

		if($validator === null || !is_object($validator)){
			$validator = new ValidateAccess();
		}
			
		$payload = self::getTokenPayload($u->secretKey);
		
		if(!is_object($payload)){
			
			Dotz::module('view')->json([
				'status' => 'error',
				'message' => $payload // $payload now carries an error message
			]);
			
			return false; 
		}

		if(!$validator->checkTokenAgainstDB($payload, $level)){
			
			Dotz::module('view')->json([
				'status' => 'error',
				'message' => 'Your account lacks proper permissions to access this resource. Request denied.'
			]);
			
			return false; 
		}

		return true;
	}


	/**
	 * Decodes User Access Token. 
	 * 
	 * Returns payload object upon success.
	 * 
	 * @param $secretKey - string [secret key]
	 */
	public static function getTokenPayload($secretKey){

		$auth = Dotz::module('input')->header('authorization');

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

}