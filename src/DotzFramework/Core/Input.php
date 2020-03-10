<?php
namespace DotzFramework\Core;

use DotzFramework\Utilities\CSRF;

class Input {

	protected static $secureInstance;

	/**
	 * If $onlyGet is set to true. Only get values can be 
	 * retrieved under the secure instance of this object.
	 * Post values are blocked due to a missing CSRF token.
	 */
	protected $onlyGet;

	/**
	 * If set to true. The $onlyGet option cannot be used.
	 * All get and post values under the secure instance of this 
	 * object would require a valid token.
	 */
	protected $tokenRequired;

	public function __construct($onlyGet = false, $tokenRequired = false){
		
		$this->onlyGet = $onlyGet;
		$this->tokenRequired = $tokenRequired;

	}

	public function verySecure(){

		return $this->secure(true);

	}

	/**
	 * Adds a CSRF check.
	 * If tokenized forms are enabled then that check is also
	 * performed here.
	 */
	public function secure($tokenRequired = false){

		$storedTokenRequiredValue = (empty(self::$secureInstance)) ? false : self::$secureInstance->tokenRequired;

		if(empty(self::$secureInstance) || $storedTokenRequiredValue !== $tokenRequired){

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
			// to determine weather to set $onlyGet to true.
			$onlyGet = false;
			$a = ($tokenRequired === true) ? 1 : 0;
			$b = (empty($jwt) && empty($jwtG)) ? 0 : 2;

			if($b == 2){
				$jwt = (empty($jwt)) ? $jwtG : $jwt;
			}

			if($formTokenization === true || $formTokenization === 'true'){

				if(($a + $b) === 0){
					$onlyGet = true;
				}else{
					if(!CSRF::validateToken($jwt)){
						throw new \Exception('Invalid CSRF token passed. Exiting.');
					}
				}

			}

			return self::$secureInstance = new Input($onlyGet, $tokenRequired);

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

		if($this->onlyGet === false){
			
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

}