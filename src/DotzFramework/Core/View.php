<?php
namespace DotzFramework\Core;

class View{

	public $configs;

	public function __construct(){
		$this->configs = Dotz::get()->load('configs')->props;
	}

	/**
	 * Use for HTML outputs.
	 * 
	 * Params:
	 *  - $view - view file name (without the extension)
	 *  - $app - object/array/data to be passed along to the view file.
	 */
	public function load($view, $app = null){
		if(!$this->viewsConfigsOk()){
			throw new \Exception('Views configurations not set correctly.');
		}

		$path = trim($view, '/');
		$file = $this->configs->app->systemPath .
				'/'. $this->configs->views->directory .
				'/'. $path .'.php';

		if(!file_exists($file)){
			throw new \Exception('View not found in View::load().');			
		}

		$dotz = new \stdClass();
		$dotz->configs = Dotz::get()->load('configs')->props;
		$dotz->url = $dotz->configs->app->httpProtocol .
				'://'. $dotz->configs->app->url;

		$dotz->viewsUrl = $dotz->configs->app->httpProtocol .
				'://'. $dotz->configs->app->url .
				'/'. $dotz->configs->views->directory;

		include_once($file);
	}

	/**
	 * Use for jSON outputs.
	 */
	public function sendToJson($data){
		$o = json_encode($data);
		
		if($o){
			header('Content-Type: application/json');
			echo $o;
			die();
		}
	}

	protected function viewsConfigsOk(){
		if(isset($this->configs->views)
				&& is_object($this->configs->views)){

			if(isset($this->configs->app->systemPath)){
				if(file_exists($this->configs->app->systemPath)){
					return true;
				}
			}
		}

		return false;
	}

}