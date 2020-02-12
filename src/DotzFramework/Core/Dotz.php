<?php
namespace DotzFramework\Core;

use Pimple\Container;
use DotzFramework\Core\Configurations;
use Symfony\Component\HttpFoundation\Request;

class Dotz {

	protected static $instance;

	public $container;

	protected function __construct($configsLocation){

		$this->container = new Container();

		$this->container['configs'] = function($c) use($configsLocation){
									return new Configurations($configsLocation);
								};

		$this->container['request'] = function($c){
									return new Request($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER);
								};

	} 

	public static function get($configsLocation = null){

		if(empty(self::$instance)){
			return self::$instance = new Dotz($configsLocation);
		}else{
			return self::$instance;
		}

	}
}