<?php
namespace DotzFramework\Core;

use DotzFramework\Core\Dotz;

/**
 * As things stand, DotzFramework only supports PDO.
 * PDO supports quite a few data sources including MySQL and Oracle
 */
class DB {

	/**
	 * holds the PDO connection instance
	 */
	public $connection;

	public function __construct(){
		
		$dotz = Dotz::get();
		$c = $dotz->container['configs']->props->db;

		$dsn = $this->getDataSourceName($c);

		$this->connection = new \PDO($dsn, $c->user, $c->password);
	}

	public function getDataSourceName($configs){
		
		if($configs->driver === 'mysql'){
			return $configs->driver.':dbname='.$configs->name.';host='.$configs->host;
		}

		return null;
	}


}