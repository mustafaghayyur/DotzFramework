<?php
namespace DotzFramework\Core;

use DotzFramework\Core\Dotz;

/**
 * A wrapper for PDO. Each database type will have its own
 * extension to this class.  
 */
class Query {

	/**
	 * Holds the PDO instance
	 */
	public $connection;

	/**
	 * Holds all Query Definition Classes' instances
	 */
	public $queryClass;

	/**
	 * To be extended in children classes.
	 * $this->connection would have to be defined.
	 */
	public function __construct(){

		$this->queryClass = [];
	
	}

	/**
	 * Fetches query from application's defined Queries.
	 */
	public function fetchQuery($class, $property) {

		if(isset($this->queryClass[$class])){
			
			return isset($this->queryClass[$class]->$property) 
				? $this->queryClass[$class]->$property 
				: null;
		
		}else{
			
			$namespace = trim(
				Dotz::get()->load('configs')->props->app->queryClassesNamespace, 
				'\\'
			);

			$className = $namespace .'\\'. ucfirst($class);
			$this->queryClass[$class] = new $className;

			return $this->queryClass[$class]->$property;
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
	 * Executes an un-preprared query, carrying raw parameters, directly injected
	 * into the query string.
	 */
	public function raw($query, $flags = \PDO::FETCH_ASSOC){
		$r = $this->connection->query($query);

		return $r->fetchAll($flags);
	}

	/**
	 * Quotes a string for querying purposes.
	 */
	public function quote($string, $flags = \PDO::PARAM_STR){
		return $this->connection->quote($string, $flags);
	}

	/**
	 * Good for generating a string of '?' placeholders for prepared statements.
	 * Useful for queries like SELECT * ... WHERE col1 IN ([array]);
	 */
	public function generatePlaceHolders($array){
	
		return  implode(',', array_fill(0, count($array), '?'));

	}

}