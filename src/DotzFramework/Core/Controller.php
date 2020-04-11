<?php
namespace DotzFramework\Core;

/**
 * Base controller class.
 *
 * Provides useful properties for ease of coding.
 *
 * Can be extended in your app, and the extension can be used
 * as your new base for all controllers.
 */
class Controller{

	public $input;

	public $view;
	
	public $query;

	public $configs;

	public function __construct(){
		$this->input = Dotz::module('input');
		$this->view = Dotz::module('view');
		$this->query = Dotz::module('query');
		$this->configs = Dotz::module('configs')->props;
	}

}