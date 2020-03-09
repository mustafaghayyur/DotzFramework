<?php
namespace DotzFramework\Core;

class Controller{

	public $input;

	public $view;
	
	public $query;

	public function __construct(){
		$this->input = Dotz::get()->load('input');
		$this->view = Dotz::get()->load('view');
		$this->query = Dotz::get()->load('query');
	}

}