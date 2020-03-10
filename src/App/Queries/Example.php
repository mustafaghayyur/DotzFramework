<?php
namespace App\Queries;

class Example {

	/**
	 * Declare all queries, each with a unique property name.
	 */
	public function __construct(){

		$this->get = 'SELECT * FROM test_table WHERE id = ?;';
	
	}

}