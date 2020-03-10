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
	 *  - $packet - Array of all vars you wish to pass to the view.
	 */
	public function load($view, Array $packet = null){
		
		if(!$this->viewsConfigsOk()){
			throw new \Exception('Views configurations not set correctly.');
		}

		$path = trim($view, '/');
		$dotzViewFile = $this->configs->app->systemPath .
				'/'. $this->configs->views->directory .
				'/'. $path .'.php';

		if(!file_exists($dotzViewFile)){
			throw new \Exception('View not found in View::load().');			
		}

		if(is_array($packet)){

			if(isset($packet['dotzViewFile'])){
				throw new \Exception('The key $packet[\'dotzViewFile\'] cannot be used in views. Exiting.');
			}

			extract($packet);
		}

		$dotz = $this->generateSystemVars();
		
		include_once($dotzViewFile);
	}

	/**
	 * Use for jSON outputs.
	 */
	public function json($data){
		$o = json_encode($data);
		
		if($o){
			header('Content-Type: application/json');
			echo $o;
			die();
		}
	}

	protected function generateSystemVars(){

		$dotz = new \stdClass();
		
		$dotz->configs = $this->configs;
		
		$dotz->url = $dotz->configs->app->httpProtocol .'://'. $dotz->configs->app->url;
		
		$dotz->viewsUrl = $dotz->url .'/'. $dotz->configs->views->directory;

		$js = Dotz::get()->load('js');
		$js->add('configs-for-js', 'var dotz = '.json_encode($dotz->configs->js)).';';
		
		$dotz->js = $js->stringify();

		return $dotz;
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