<?php
namespace App\Queries;

class Example {

	/**
	 * Declare all queries, each with a unique property name.
	 */
	public function __construct(){

		$this->get = 'SELECT * FROM test_table WHERE id = ?;';
		
		$this->insert = 'INSERT INTO test_table (title) VALUES (?);';

		$this->update = 'UPDATE test_table SET title = ? WHERE id = ?;';
	
	}

}