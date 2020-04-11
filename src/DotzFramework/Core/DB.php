<?php
namespace DotzFramework\Core;

/**
 * As things stand, DotzFramework only supports PDO.
 * We have tried to accomodate MySQL, PostgreSQL and MSSQL below.
 */
class DB {

	/**
	 * Holds the PDO connection instance.
	 * Can be overwritten with a custom PDO instantiation in your code.
	 */
	public $connection;

	public function __construct($overwite = false){
		
		// $overwrite allows you to instantiate PDO directly into DB::$connection.
		// with your own configurations.
		if($overwite){
			return true;
		}

		$c = Dotz::config('db');

		$dsn = $this->getDataSourceName($c);

		if($c->driver === 'pgsql'){
			$this->connection = new \PDO($dsn);
		}else{
			$this->connection = new \PDO($dsn, $c->user, $c->password);
		}

		
	}

	public function getDataSourceName($configs){
		
		$dsn = null;

		if($configs->driver === 'mysql'){
			$dsn = $configs->driver.':dbname='.$configs->name.';host='.$configs->host;

			if(isset($configs->port)){
				$dsn .= ';port='.$configs->port;
			}
		}

		if($configs->driver === 'pgsql'){
			$dsn = $configs->driver.':host='.$configs->host.';dbname='.$configs->name.';user='.$configs->user.';password='.$configs->password;

			if(isset($configs->port)){
				$dsn .= ';port='.$configs->port;
			}
		}

		if($configs->driver === 'sqlsrv'){
			$dsn = $configs->driver.':Server='.$configs->host;

			if(isset($configs->port)){
				$dsn .= ','.$configs->port;
			}

			$dsn .= ';Database='.$configs->name;
		}

		return $dsn;
	}


}