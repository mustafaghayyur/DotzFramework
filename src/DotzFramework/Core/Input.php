<?php
namespace DotzFramework\Core;

use DotzFramework\Utilities\CSRF;

/**
 * This is not a traditional singleton class.
 * It however uses the static instance of itself to hold
 * settings used for secure retrieval of GET and POST variables.
 */
class Input {

	/**
	 * Holds an instance of itself.
	 */
	protected static $secureInstance;

	/**
	 * If set to true POST values cannot be retrieved under the
	 * secure instance of this object.
	 * 
	 * Post values are blocked due to a missing CSRF token.
	 */
	protected $onlySecureGetAllowed;

	/**
	 * If set to true the $onlySecureGetAllowed option cannot be used.
	 * GET variables would also require a valid CSRF token.
	 */
	protected $tokenRequiredForSecureInstance;

	public function __construct($onlySecureGetAllowed = false, $tokenRequiredForSecureInstance = false){
		
		$this->onlySecureGetAllowed = $onlySecureGetAllowed;
		$this->tokenRequiredForSecureInstance = $tokenRequiredForSecureInstance;

	}

	/**
	 * Wrapper function for setting $tokenRequiredForSecureInstance to true
	 * in the secure method. Useful for ensuring a get variable
	 * is only retrieved if a valid token exists.
	 */
	public function verySecure(){

		return $this->secure(true);

	}

	/**
	 * Adds a CSRF check.
	 * If tokenized forms are enabled then that check is also
	 * performed here.
	 */
	public function secure($tokenRequiredForSecureInstance = false){

		$storedTokenRequiredValue = (empty(self::$secureInstance)) ? false : self::$secureInstance->tokenRequiredForSecureInstance;

		if(empty(self::$secureInstance) || $storedTokenRequiredValue !== $tokenRequiredForSecureInstance){

			$csrf = Dotz::get()->load('configs')->props->app->csrfCheck;
			$formTokenization = Dotz::get()->load('configs')->props->app->formTokenization;

			if($csrf === true || $csrf === 'true'){
				if(CSRF::checkOrigin() === false){
					throw new \Exception('Could not pass CSRF security check. Exiting.');
				}
			}

			$jwt = $this->post('jwt', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			$jwtG = $this->get('jwt', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

			// Below we create logic similar to bitwise logic,
			// to determine value of $onlySecureGetAllowed.
			$onlySecureGetAllowed = false;
			$a = ($tokenRequiredForSecureInstance === false) ? 0 : 1;
			$b = (empty($jwt) && empty($jwtG)) ? 0 : 2;

			if($b == 2){
				$jwt = (empty($jwt)) ? $jwtG : $jwt;
			}

			if($formTokenization === true || $formTokenization === 'true'){

				if(($a + $b) === 0){
					$onlySecureGetAllowed = true;
				}else{
					if(!CSRF::validateToken($jwt)){
						throw new \Exception('Invalid CSRF token passed. Exiting.');
					}
				}

			}

			return self::$secureInstance = new Input($onlySecureGetAllowed, $tokenRequiredForSecureInstance);

		}else{
			return self::$secureInstance;
		}
		
	}

	/**
	 * A shorthand for the Symfony HTTP Foundation's filter() method.
	 * Filters can be referenced from:
	 * https://www.php.net/manual/en/filter.filters.php
	 */
	public function get($key, $filter = null, $options = []){

		if($filter === null){
			$xss = Dotz::get()->load('configs')->props->app->xssCheck;

			if($xss === true || $xss === 'true'){
				$filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS;
			}
		}

		// if $filter is still null or false...
		if($filter === false || $filter === null){
			$filter = FILTER_DEFAULT; // don't filter
		}

		return Dotz::get()->load('request')->query->filter($key, '', $filter, $options);
	}

	/**
	 * A shorthand for the Symfony HTTP Foundation's filter() method.
	 * Filters can be referenced from:
	 * https://www.php.net/manual/en/filter.filters.php
	 */
	public function post($key, $filter = null, $options = []){

		if($this->onlySecureGetAllowed === false){
			
			if($filter === null){
				$xss = Dotz::get()->load('configs')->props->app->enableXSSCheck;

				if($xss === true || $xss === 'true'){
					$filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS;
				}
			}

			// if $filter is still null or false...
			if($filter === false || $filter === null){
				$filter = FILTER_DEFAULT; // don't filter
			}

			return Dotz::get()->load('request')->request->filter($key, '', $filter, $options);
		
		}else{
			
			throw new \Exception('Cannot retrieve a POST value without a valid CSRF token.');
		}
		
	}

	/**
	 * Grabs the HTTP header you identify with $key.
	 * 
	 * Note: the Authorization header is not included
	 * in the PHP global variable $_SERVER. 
	 * 
	 * Requires some re-configuration to access.
	 */
	public function header($key = ''){
		return Dotz::get()->load('request')->headers->get($key);
	}

}