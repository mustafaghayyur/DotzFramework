<?php
namespace DotzFramework\Core;

class Input {

	/**
	 * A shorthand for the Symfony HTTP Foundation's filter() method.
	 * Filters can be referenced from:
	 * https://www.php.net/manual/en/filter.filters.php
	 * To filter nothing use filter = FILTER_DEFAULT
	 */
	public function get($key, $filter = FILTER_SANITIZE_SPECIAL_CHARS){
		if($filter === 'none'){
			$filter = FILTER_DEFAULT;
		}
		
		return Dotz::get()->load('request')->query->filter($key, '', $filter);
	}

	/**
	 * A shorthand for the Symfony HTTP Foundation's filter() method.
	 * Filters can be referenced from:
	 * https://www.php.net/manual/en/filter.filters.php
	 * To filter nothing use filter = FILTER_DEFAULT
	 */
	public function post($key, $filter = FILTER_SANITIZE_SPECIAL_CHARS){
		if($filter === 'none'){
			$filter = FILTER_DEFAULT;
		}

		return Dotz::get()->load('request')->request->filter($key, '', $filter);
	}

}