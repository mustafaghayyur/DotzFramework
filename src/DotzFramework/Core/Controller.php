<?php
namespace DotzFramework\Core;

class Controller{

	public $view;
	
	public $model;

	public function __construct(){
		$this->view = Dotz::get()->load('view');
		$this->model = Dotz::get()->load('model');
	}

}