<?php
namespace DotzFramework\Core;

use DotzFramework\Core\Dotz;


/**
 * This is the parent Query class meant to give the entire data 
 * modeling some uniformity. Each database type will have its own
 * extension to this class.  
 */
class Query {

	/**
	 * Holds the PDO instance
	 */
	public $connection;

	/**
	 * Holds all used models' instances
	 */
	public $models;

	public function __construct(){

		// $connection now holds the connection instance held by DB::connection
		$this->connection = Dotz::get()->load('db')->connection;

		$this->models = [];

	}

	/**
	 * Fetches query from application's defined Models.
	 */
	public function fetchQuery($model, $key) {

		if(isset($this->models[$model])){
			
			return isset($this->models[$model]->$key) ? $this->models[$model]->$key : null;
		
		}else{
			
			$namespace = trim(
								Dotz::get()->container['configs']->props->models->namespace, 
								'\\'
							);

			$class = $namespace .'\\'. ucfirst($model);
			$this->models[$model] = new $class;

			return $this->models[$model]->$key;
		}

		return null;
	}

	/**
	 * Processes a query string as a prepared statement. All values must be 
	 * extrapulated from the query string and passed seperately as an array of inputs.
	 */
	public function execute($query, $data = [], $flags = \PDO::FETCH_ASSOC){

		$r = $this->connection->prepare($query);
		$r->execute($data);

		return $r->fetchAll($flags);

	}

	/**
	 * Good to get string of '?' placeholders for prepared statements.
	 * Useful for queries like SELECT ... WHERE col1 IN ([array]);
	 */
	public function getPlaceHolders($array){
	
		return  implode(',', array_fill(0, count($array), '?'));

	}

}