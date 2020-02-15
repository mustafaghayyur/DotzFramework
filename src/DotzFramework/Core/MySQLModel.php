<?php
namespace DotzFramework\Core;

/**
 *  Some logic is split between this and parent class.
 *  Note: $this->db holds a PDO instance.
 */
class MySQLModel extends Model {


	public function __construct(){
		parent::__construct();		
	}

	/**
	 * Processes a query as a prepared statement. All values must be 
	 * extrapulated from the query string and passed as an array.
	 */
	public function query($query, $data = [], $flags = \PDO::FETCH_ASSOC){

		if(isset($this->queries[$query])){
			$query = $this->queries[$query];
		}

		$r = $this->db->prepare($query);
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