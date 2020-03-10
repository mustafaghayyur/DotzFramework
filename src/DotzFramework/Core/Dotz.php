<?php
namespace DotzFramework\Core;

use Pimple\Container;

class Dotz {

	protected static $instance;

	protected $container;

	protected function __construct(){

		$this->container = new Container();
		$this->load('configs');
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
}