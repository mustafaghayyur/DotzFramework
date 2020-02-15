<?php
namespace DotzFramework\Core;

use DotzFramework\Core\Dotz;


/**
 * This is the parent Model class meant to give the entire data 
 * modeling some uniformity. Each database type will have its own
 * extension to this class.  
 */
class Model {

	public $db;

	/**
	 * $queries will hold often used queries that developers
	 * wish to store in a central place. Values can be added to this 
	 * property anywhere in the app.
	 */
	protected $queries;


	public function __construct(){

		// $db now holds the connection instance held by DB::connection
		$this->db = Dotz::get()->load('db')->connection;

		$this->queries = [];

	}

	public function saveQuery($identifier, $query){
		
		if(is_string($identifier)){
			if(is_string($query)){
				$this->queries[$identifier] = $query;
				return true;
			}
		}

		return false;
	}

}