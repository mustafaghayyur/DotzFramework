<?php
namespace DotzFramework\Core;

use Pimple\Container;

class Dotz {

	protected static $instance;

	protected $container;

	protected function __construct(){

		$this->container = new Container();
		$this->load('request');

	} 

	/**
	 * Loads a conatiner module. If the module is already loaded;
	 * returns its stored instance.
	 */
	public function load($key, $functionDefinition = false){
		
		if(!isset($this->container[$key])){
			
			$functionDefinition = (!$functionDefinition) ? \ModulesDefinitions::fetch($key) : $functionDefinition;
			$this->container[$key] = $functionDefinition;
		
		}
		
		return $this->container[$key];
	}

	/**
	 * Retrieves the stored instance of this Class (Dotz()).
	 * A Singleton pattern.
	 */
	public static function get($configsLocation = null){

		if(empty(self::$instance)){
			return self::$instance = new Dotz($configsLocation);
		}else{
			return self::$instance;
		}

	}

	/**
	 * PHP notices for undefined index/properties are annoying.
	 * This static method helps in preventing such flags.
	 *
	 * Dotz::grabKey($variable, 'key'); returns the value of the key.
	 */
	public static function grabKey($variable, $key){

		if(is_array($variable)){
			return (isset($variable[$key])) ? $variable[$key] : null;
		}

		if(is_object($variable)){
			return (isset($variable->{$key})) ? $variable->{$key} : null;
		}

		return null;

	}
}