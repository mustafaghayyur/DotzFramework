<?php
use DotzFramework\Core;
use DotzFramework\Utilities;
use DotzFramework\Modules;
use Symfony\Component\HttpFoundation\Request;

/**
 * Define over here any app-wide modules you wish to 
 * make available to $dotz->container
 *   
 * Below you can define your own modules to mount to 
 * the container. Follow the below pattern to define 
 * a new module:
 * 
 *   $mods['uniqueKey'] = function($c) {
 *  		return new MyClass();
 *   }
 *
 * where:
 *  - $mods is the array carrying all the definitions
 *  - 'uniqueKey' is a string identifer which you will use 
 *    to load/use this module
 *  - MyClass is the name of the class for your module. Be 
 *    sure to account for php namespaces if they are used.
 *
 *
 * You can then load a module anywhere in your application, using:
 *   $dotz = Dotz::get();
 *   $dotz->load('uniqueKey');
 *   // Then use it...
 *   $dotz->container['uniqueKey']->someMyClassMethod();
 * 
 */
class ModulesDefinitions {

	public static function fetch ($key = null){
		
		$mods = [];

		$mods['configs'] = function($c){ 
			return new Core\Configurations(__DIR__ . '/configs'); 
		};

		$mods['request'] = function($c){
			return new Request($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER);
		};

		$mods['view'] = function($c){
			return new Core\View();
		};

		/**
		 * Your application may have more than one data sources
		 * requiring additional instances of an appropirate Query class
		 */
		$mods['query'] = function($c){
			return new Modules\Query\MySQLQuery();
		};



		/**
		 * =====================================================
		 * 	DO NOT EDIT BELOW THIS LINE:
		 * =====================================================
		 */
		if(empty($key)){
			return $mods;
		}else{
			return $mods[$key];			
		}
	}

}