<?php
namespace DotzFramework\Core;

class View{

	public $configs;

	/**
	 * Use for HTML outputs.
	 * 
	 * Params:
	 *  - $view - view file name (without the extension)
	 *  - $packet - Array of all vars you wish to pass to the view.
	 */
	public function load($view, Array $packet = null){

		$this->configs = Dotz::config('app');

		if(!$this->viewsConfigsOk()){
			throw new \Exception('Views configurations not set correctly.');
		}

		$path = trim($view, '/');
		$dotzViewFile = $this->configs->systemPath .
				'/'. $this->configs->viewsDir .
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
						
		$dotz->url = Dotz::config('app.httpProtocol') .'://'. Dotz::config('app.url');
		
		$dotz->viewsUrl = $dotz->url .'/'. Dotz::config('app.viewsDir');

		$js = Dotz::get()->load('js');
		$js->add(
			'configs-for-js', 
			'var dotz = '. json_encode(Dotz::config('js')) .';'
		);
		
		$dotz->js = $js->stringify();

		return $dotz;
	}

	protected function viewsConfigsOk(){
		if(isset($this->configs->viewsDir)){

			if(isset($this->configs->systemPath)){
				
				$f = $this->configs->systemPath .'/'. $this->configs->viewsDir;
				
				if(file_exists($f)){
					return true;
				}
			}
		}

		return false;
	}

}