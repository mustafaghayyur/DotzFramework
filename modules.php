<?php
use DotzFramework\Core;
use DotzFramework\Utilities;
use DotzFramework\Modules;
use Symfony\Component\HttpFoundation\Request;

/**
 * Define over here any app-wide modules you wish to make available to:
 *   $dotz->container
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

		$mods['db'] = function($c){
			// If you needed to instantiate a custom PDO connection, you would pass
			// $overwrite = true as a parameter to Core\DB(). Then assign the 
			// appropriate value to DB::$connection appropriately in your app, like so:
			// 
			//   $dotz = Dotz::get();
			//   $dotz->container['db']->connection = \PDO($dns, $user, $pass);
			// 
			// This will configure the right database to your controller and models
			// as well.
			return new Core\DB($overwrite = false);
		};

		/**
		 * =============================================================
		 * 			^ DO NOT MODIFY ANYTHING ABOVE THIS LINE ^
		 * =============================================================
		 */

		/**
		 * Below you can define your own modules to mount to 
		 * the container. Follow the below pattern to define 
		 * a new module:
		 * 
		 *   $mods['unique_key'] = function($c) {
		 *  		return new ClassName();
		 *   }
		 *
		 * where:
		 *  - $mods is the array carrying all the function-definitions
		 *  - 'unique_key' is a string identifer which you will use 
		 *    to load/use this module
		 *  - ClassName is the name of the class for your module. Be 
		 *    sure to account for php namespaces if they are used.
		 *
		 *
		 * You can then load a module anywhere in your application, using:
		 *   $dotz = Dotz::get();
		 *   $dotz->load('unique_key');
		 *
		 * To use the module in your application:
		 *   $dotz = Dotz::get();
		 *   $dotz->container['unique_key']->{any property/method this module has.}();
		 * 
		 */
		
		// Your application may have more than one data sources
		// requiring additional instances of an appropirate Model class
		$mods['query'] = function($c){
			return new Core\MySQLQuery();
		};



		/**
		 * =====================================================
		 * 	DO NOT EDIT BELOW THIS LINE:
		 * 	====================================================
		 */
		if(empty($key)){
			return $mods;
		}else{
			return $mods[$key];			
		}
	}

}