<?php
namespace DotzFramework\Core;

use Pimple\Container;

/**
 * The Dotz Service Containers powered by Pimple.
 * Should be used throughout the framework and your 
 * application. 
 * 
 * Useful for calling modules defined in
 * modules.php, and accessing system configurations
 * defined in the configs/ directory.
 */
class Dotz {

	/**
	 * It's a singleton pattern. 
	 * Holds class instance.
	 */
	protected static $instance;

	/**
	 * Holds the service container.
	 * Container modules are best accessed using: 
	 * Dotz::module('contianer-key');
	 */
	protected $container;

	/**
	 * Instantiates the container inside Dotz::$container property.
	 */
	protected function __construct(){

		$this->container = new Container();
		$this->load('request');

	} 

	/**
	 * Grabs an instance of any module defined in modules.php
	 *
	 * added in Dotz v0.2.3
	 *
	 * Replaces the need to call Dotz::get()->load().
	 */
	public static function module($key, $functionDefinition = false){
		
		if(empty(self::$instance)){
			self::$instance = new Dotz();
		}

		if(!isset(self::$instance->container[$key])){
			
			$functionDefinition = (!$functionDefinition) ? \ModulesDefinitions::fetch($key) : $functionDefinition;

			self::$instance->container[$key] = $functionDefinition;
		
		}
		
		return self::$instance->container[$key];
	}

	/**
	 * Retrieves a config property.
	 * 
	 * The $path should be formatted like so:
	 * 	
	 * 	'fileName.property.propertyChild...nth-Property'
	 * 	
	 * For example: $path = 'app.url' -> refers to the configs/app.txt
	 * file, and 'url' is the property conatained within it.
	 *
	 * Leave $obj empty.
	 */
	public static function config($path, $obj = 'init'){

		if($obj === 'init'){
			$obj = Dotz::module('configs')->props;
		}

		preg_match('#([^\.]*).?(.*)?#', $path, $m);

		$n = self::grabKey($obj, $m[1]);

		if(strlen($m[2]) > 0){
			return self::config($m[2], $n);
		}else{
			return $n;
		}

	}

	/**
	 * PHP notices for undefined index/properties are annoying.
	 * 
	 * This static method helps in preventing such flags.
	 *
	 * Dotz::grabKey($variable, 'key'); 
	 *  - Returns the value of the key. 
	 *  - Null if property doesn't exist.
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

	/**
	 * Retrieves the stored instance of this Class, Dotz().
	 * This is a singleton pattern.
	 */
	public static function get(){

		if(empty(self::$instance)){
			return self::$instance = new Dotz();
		}else{
			return self::$instance;
		}

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
}