<?php
namespace DotzFramework\Core;

class Controller{

	public $view;
	
	public $query;

	public function __construct(){
		$this->input = new Input();

		$this->view = Dotz::get()->load('view');
		$this->query = Dotz::get()->load('query');
	}

}