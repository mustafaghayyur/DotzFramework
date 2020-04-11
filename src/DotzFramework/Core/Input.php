<?php
namespace DotzFramework\Core;

use DotzFramework\Utilities\CSRF;

/**
 * The Input class provides more secure alternatives to
 * $_GET and $_POST global variables.
 *
 * It also has the ability to grab HTTP headers.
 */
class Input {

	/**
	 * Holds an instance of itself.
	 * This is not a traditional singleton class.
	 * It however uses the static instance of itself to hold
	 * settings used for secure retrieval of GET and POST variables.
	 */
	protected static $secure;

	/**
	 * Security level:
	 *  - 0 = no CSRF token check
	 *  - 1 = CSRF token check only for POST
	 *  - 2 = CSRF token check for GET & POST both
	 */
	protected $level;

	/**
	 * Stores the JWT token
	 */
	protected $jwt;

	public function __construct($level = 0, $jwt = null){
		
		$this->level = $level;
		$this->jwt = $jwt;

	}

	/**
	 * Wrapper function for setting $secureGetNotAllowed to true
	 * in the secure method. Useful for ensuring a get variable
	 * is only retrieved if a valid token exists.
	 */
	public function verySecure(){

		return $this->secure(2);

	}

	/**
	 * Adds a CSRF check.
	 * If tokenized forms are enabled then information and settings
	 * are applied for that as well.
	 */
	public function secure($level = 1){

		if($level === false){
			$level = 1;
		}

		if($level === true){
			$level = 2;
		}

		if(!is_int($level) || $level < 1){
			throw new \Exception("Security level cannot be set to lower than 1 in Input::secure(). Exiting.");
		}

		$stored = empty(self::$secure) ? false : self::$secure->level;

		if(empty(self::$secure) || $level !== $stored){

			$csrf = Dotz::config('app.csrf.check');
			$tokenization = Dotz::config('app.csrf.tokenization');

			// we made a few mods to the configs/app.txt settings structure:
			if($csrf === null || $tokenization === null){
				throw new \Exception(ErrorHandler::ERROR1);
			}


			if($csrf === true || $csrf === 'true'){
				if(CSRF::checkOrigin() === false){
					throw new \Exception('Could not pass Same-Origins CSRF security check. Exiting.');
				}
			}

			if($tokenization === true || $tokenization === 'true'){
				
				if(empty(self::$secure->jwt)){
					
					$jwt = $this->post('jwt', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
				
					if(empty($jwt)){
						$jwt = $this->get('jwt', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
					}

				}else{
					$jwt = self::$secure->jwt;
				}

				if($level === 2 || !empty($jwt)){
					if(!CSRF::validateToken($jwt)){
						throw new \Exception('Invalid CSRF token passed; cannot retrieve HTTP request data securely. Exiting.');
					}
				}
				
				$jwt = empty($jwt) ? null : $jwt;

			}else{
				$level = 0;
				$jwt = null;
			}

			return self::$secure = new Input($level, $jwt);

		}else{
			return self::$secure;
		}
		
	}

	/**
	 * Retrieves the requested $_GET value.
	 * 
	 * Note: you probably would want to use try{}catch{} with this!
	 * Throws exceptions!
	 * 
	 * Uses Symfony's HTTP Foundation's filter() method.
	 * Filters can be referenced from:
	 * https://www.php.net/manual/en/filter.filters.php
	 */
	public function get($key, $filter = null, $options = []){

		if(!$this->ok('get')){
			throw new \Exception('Cannot retrieve a GET value without a valid CSRF token.');
		}

		if($filter === null){
			$xss = Dotz::config('app.xssCheck');

			if($xss === true || $xss === 'true'){
				$filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS;
			}
		}

		// if $filter is still null or false...
		if($filter === false || $filter === null){
			$filter = FILTER_DEFAULT; // don't filter
		}

		return Dotz::module('request')->query->filter($key, '', $filter, $options);
	}

	/**
	 * Retrieves the requested $_POST value.
	 * 
	 * Note: you probably would want to use try{}catch{} with this!
	 * Throws exceptions!
	 * 
	 * Uses Symfony's HTTP Foundation's filter() method.
	 * Filters can be referenced from:
	 * https://www.php.net/manual/en/filter.filters.php
	 */
	public function post($key, $filter = null, $options = []){

		if(!$this->ok('post')){
			throw new \Exception('Cannot retrieve a POST value without a valid CSRF token.');
		}
			
		if(is_object($filter) && method_exists($filter, 'process')){
			
			$v = Dotz::module('request')->request
					->filter($key, '', FILTER_DEFAULT, $options);
					
			return $filter->process($key, $v);
		}

		if($filter === null){
			$xss = Dotz::config('app.xssCheck');

			if($xss === true || $xss === 'true'){
				$filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS;
			}
		}

		// if $filter is still null or false...
		if($filter === false || $filter === null){
			$filter = FILTER_DEFAULT; // don't filter
		}

		return Dotz::module('request')->request->filter($key, '', $filter, $options);
		
	}

	/**
	 * Grabs the HTTP header you identify with $key.
	 * 
	 * Note: the Authorization header is not included
	 * in the PHP global variable $_SERVER. 
	 * Requires some re-configuration to access.
	 *
	 * Uses Symfony's HTTP Foundation.
	 */
	public function header($key = ''){
		return Dotz::module('request')->headers->get($key);
	}

	/**
	 * Is it okay to retrieve this value given all the
	 * security circumtances?
	 *
	 * That is what this method determines.
	 */
	protected function ok($method){
		if($this->ignoreToken($method)){
			return true;
		}else{
			return empty($this->jwt) ? false : true;
		}
	}

	/**
	 * Should we ignore the CSRF Token given all
	 * the settings passed down to us?
	 *
	 * That is what this method determines.
	 */
	protected function ignoreToken($method = 'get'){
		if($this->level === 0){
			return true;
		}

		if($this->level === 1){
			if($method === 'get'){
				return true;
			}
			if($method === 'post'){
				return false;
			}
		}

		return false;
	}

}