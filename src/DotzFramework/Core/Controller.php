<?php
namespace DotzFramework\Core;

class Controller{

	public $input;

	public $view;
	
	public $query;

	public $configs;

	public function __construct(){
		$this->input = Dotz::get()->load('input');
		$this->view = Dotz::get()->load('view');
		$this->query = Dotz::get()->load('query');
		$this->configs = Dotz::get()->load('configs')->props;
	}

}