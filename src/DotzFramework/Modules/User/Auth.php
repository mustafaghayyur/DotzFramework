<?php
namespace DotzFramework\Modules\User;

use DotzFramework\Core\Dotz;

/**
 * Parent Auth Class
 *
 * All responses should be passed down in $message & $status props.
 */
class Auth {

	/**
	 * Used to temporarily store a user record while completing a request.
	 */
	public $userRecord;

	/**
	 * carries a message to be accessible in the 
	 * script you instantiate this class with.
	 */
	public $message;


	public function __construct($ignoreSetup = false){
		
		if($ignoreSetup === true){
			return true; // some internal function is calling this instance.
		}

		$u = Dotz::config('user');

		if($u === null){
			// User module has not been activated yet. Setup...
			Setup::install();
		}
	}

	/**
	 * The core of the login function in both TokenAuth & SessionAuth classes.
	 *
	 * Validates the supplied username and password against the database.
	 * 
	 * @param $user - string [username]
	 * @param $pass - string [password]
	 * @param $level - int [access level required to pass check]
	 * @param $validator - obj [holds a access-validation class instance]
	 * @param $validatorCall - bool [only set to true if you know what it does!]
	 */
	public function authenticateUser($user, $pass = null, $level = 3, $validator = null, $validatorCall = false){
		
		try{
	
			$q = Dotz::module('query');
			$r = $q->execute('SELECT * FROM user WHERE username = ?', [$user]);

		}catch(\Exception $e){

			preg_match('#^\[[^\]]*\] - (.*)#', $e->getMessage(), $m);
			$this->message = $m[1];
			return false;

		}

		if(!is_array($r) || count($r) === 0){
			$this->message = 'Username not found.';
			return false;
		}

		if($validatorCall === true){
			// this is an internal, partial authentication check
			// it does not need all the checks...
			// just send the db record if the user is found.
			return $r[0];
		}

		if($validator === null || !is_object($validator)){
			$validator = new ValidateAccess();
		}

		if(!$validator->checkStatus($r[0]['status'])){
			$this->message = 'Your account is not active. Cannot authenticate credentials.';
			return false;
		}

		if(!$validator->checkAccessLevel($r[0]['access_level'], $level)){
			$this->message = 'Your account does not have the correct access level. Cannot authenticate credentials.';
			return false;
		}
		
		$hash = $r[0]['password']; // db password value is in hash

		if(true === password_verify($pass, $hash)){
			
			$this->message = 'Authentication passed.';
			$this->userRecord = $r[0];
			return true;

		}else{
			$this->message = 'Password incorrect.';
			return false;
		}
	}

	/**
	 * Registers a new user. Adds their username, password and email
	 * data to the user database table.
	 *
	 * To collect additional user data, create your own additional
	 * database tables and code to handle such data, seperately.
	 * 
	 * @param $u - array [user record]
	 * @param $level - int [access level required to pass registration]
	 * @param $status - string [status of this account]
	 * @param $validator - obj [holds a user-fields-validation class instance]
	 */
	public function register(Array $u, $level = 3, $status = 'probation', $validator = null){

		try {
	
			if($validator === null || !is_obj($validator)){
				$validator = new ValidateUserFields();
			}

			$validator->email($u['email']);
			$validator->username($u['username']);
			$validator->password($u['password']);

		} catch (\Exception $e) {

			$this->message = $e->getMessage();
			return false;
		
		}

		$hash = password_hash($u['password'], PASSWORD_BCRYPT);

		try{
	
			$q = Dotz::module('query');
			
			$n = $q->execute(
				'INSERT INTO user (email, username, password, access_level, status) VALUES (?, ?, ?, ?, ?);', 
				[$u['email'], $u['username'], $hash, $level, $status ]
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

}