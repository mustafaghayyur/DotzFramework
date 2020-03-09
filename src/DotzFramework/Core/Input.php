<?php
namespace DotzFramework\Core;

use DotzFramework\Utilities\CSRF;

class Input {

	/**
	 * Adds a CSRF check.
	 * If tokenized forms are enabled then that check is also
	 * performed here.
	 */
	public function secure(){

		$csrf = Dotz::get()->load('configs')->props->app->csrfCheck;
		$formTokenization = Dotz::get()->load('configs')->props->app->formTokenization;

		if($csrf === true || $csrf === 'true'){
			if(CSRF::checkOrigin() === false){
				throw new \Exception('Could not pass CSRF security check. Exiting.');
			}
		}

		if($formTokenization === true || $formTokenization === 'true'){
			
			$jwt = $this->post('jwt', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
			
			if(!CSRF::validateToken($jwt)){
				throw new \Exception('Invalid CSRF token passed. Exiting.');
			}
		}

		return $this;
		
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
	}

}