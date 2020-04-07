<?php
namespace DotzFramework\Core;

/**
 * A wrapper for PDO. Each database type will have its own
 * extension to this class.  
 */
class Query {

	/**
	 * Holds the PDO instance
	 */
	public $pdo;
	
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
				Dotz::config('app.queryClassesNamespace'), 
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
	 *
	 * Returns an array of results | # of Rows affected
	 */
	public function execute($query, $data = [], $flags = \PDO::FETCH_ASSOC){

		$s = $this->pdo->prepare($query);
		
		if(false === $s->execute($data)){
			
			$e = $s->errorInfo();
			
			if($e[0] === '00000' || empty($e[2])){
				$e = $this->pdo->errorInfo();
			}

			throw new \Exception('[SQL Error Code: '.$e[0].'] - '.$e[2]);
		}

		if($s->columnCount() > 0){
			// the result set can be empty but would still be an array
			return $s->fetchAll($flags);
		}else{
			// the query has no result set. Return rows affected number.
			return $s->rowCount();
		}

	}

	/**
	 * Executes an un-preprared query, carrying raw parameters, directly injected
	 * into the query string.

	 * Returns an array of results | # of Rows affected
	 */
	public function raw($query, $flags = \PDO::FETCH_ASSOC){
		$s = $this->pdo->query($query);

		if($s === false){
			
			$e = $this->pdo->errorInfo();

			if($e[0] === '00000' || empty($e[2])){
				$e = $s->errorInfo();
			}

			throw new \Exception('[SQL Error Code: '.$e[0].'] - '.$e[2]);
		}

		if($s->columnCount() > 0){
			// the result set can be empty but would still be an array
			return $s->fetchAll($flags); // array
		}else{
			// the query has no result set. Return rows affected number.
			return $s->rowCount(); // int
		}
	}

	/**
	 * Quotes a string for querying purposes.
	 */
	public function quote($string, $flags = \PDO::PARAM_STR){
		return $this->pdo->quote($string, $flags);
	}

	/**
	 * Good for generating a string of '?' placeholders for prepared statements.
	 * Useful for queries like SELECT * ... WHERE col1 IN ([array]);
	 */
	public function generatePlaceHolders($array){
	
		return  implode(',', array_fill(0, count($array), '?'));

	}

}