<?php
namespace DotzFramework\Core;

class View{

	/**
	 * App's configs
	 */
	public $configs;

	/**
	 * Bool to store if the View::load() method
	 * has already been called. Used by ErrorHandler().
	 */
	public $loadCalled = false;

	/**
	 * Bool to store if the View::json() method
	 * has already been called. Used by ErrorHandler().
	 */
	public $jsonCalled = false;

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

		$this->loadCalled = true;
		
		include_once($dotzViewFile);
	}

	/**
	 * Used for jSON outputs.
	 */
	public function json($data, $httpStatusCode = null){
		
		// add PHP error notices (if any) to the response...
		if(isset(Dotz::module('error')->notices) && is_array(Dotz::module('error')->notices)){
			if(is_array($data)){
				$data['serverMessages'] = Dotz::module('error')->notices;
			}
			if(is_object($data)){
				$data->serverMessages = Dotz::module('error')->notices;
			}
		}

		$o = json_encode($data);
		
		if(json_last_error() === JSON_ERROR_NONE){
			
			$this->jsonCalled = true;


			http_response_code(
				self::httpStatusCode($httpStatusCode, $data)
			);

			header('Content-Type: application/json');

			echo $o;
			die();
		}
	}

	/**
	 * Sets the appropriate HTTP status code
	 */
	protected static function httpStatusCode($code, $data){

		if($code === null){
			
			$status = isset($data['status']) ? $data['status'] : null;
			
			if(isset($data->status)){
				$status = empty($status) ? $data->status : null;
			}

			if($status === 'error'){
				return 400;
			}
		}

		if($code === null || !is_int($code)){
			return 200;
		}

		return $code;
	}

	/**
	 * Generates the $dotz variable used in html view files.
	 */
	protected function generateSystemVars(){

		$dotz = new \stdClass();
						
		$dotz->appName = Dotz::config('app.name');
		$dotz->url = Dotz::config('app.httpProtocol') .'://'. Dotz::config('app.url');
		
		$dotz->viewsUrl = $dotz->url .'/'. Dotz::config('app.viewsDir');

		$js = Dotz::module('js');
		$js->add(
			'configs-for-js', 
			'var dotz = '. json_encode(Dotz::config('js')) .';'
		);
		
		$dotz->js = $js->stringify();

		return $dotz;
	}

	/**
	 * Ensures minimum $config settings are available.
	 */
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