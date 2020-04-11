<?php
namespace DotzFramework\Core;

/**
 * Dotz's core queries formulating library. 
 * 
 * Each database type will have its own
 * extension to this class.  
 */
class Query {

	/**
	 * Holds the PDO instance
	 */
	public $pdo;
	
	/**
	 * Holds all Query Definition Classes' instances.
	 * Those classes typically found in src/App/Queries.
	 */
	public $queryClass;

	/**
	 * To be extended in children classes.
	 * $this->pdo would have to be defined.
	 */
	public function __construct(){

		$this->queryClass = [];
	
	}

	/**
	 * Fetches a query string from your application's defined Queries.
	 * Typically defined in src/App/Queries/*.
	 *
	 * @return string|bool returns query string on success | false on failiure
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
	 * @return  array | int 
	 *          - Returns an array of results 
	 *          - or # of Rows affected on insert/update/delete operations.
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
	 * 
	 * @return  array | int 
	 *          - Returns an array of results 
	 *          - or # of Rows affected on insert/update/delete operations.
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
	 *
	 * Call like so:
	 * 	$placeholders = Query::fillers($array);
	 */
	public static function fillers($array){
	
		return  implode(',', array_fill(0, count($array), '?'));

	}

}