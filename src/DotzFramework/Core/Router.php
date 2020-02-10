<?php 
namespace DotzFramework\Core;

use \Exception;
use DotzFramework\Utilities\SymfonyRequest;

class Router {

	public $configs;

	/**
	* Sets the configs for this router.
	*/
	public function __construct($configs){
		$this->configs = $configs;
	}

	/**
	* Does the routing.
	*/
	public function do(){

		if(!$this->areConfigsDefined()){
			throw new Exception("Router configurations are not correct. (Make sure your config files don't have any json errors.)");
		}

		$uri = $this->getUri();

		// $controller will hold an array of the controller class,
		// method and an array of arguments to be passed along.
		$controller = null;

		// If there are no uri elements, load the default page.
		if(empty($uri[0])){

			$cObj = $this->instantiateClass(
						$this->configs->router->default->controller, 
						$this->configs->router->default->method
					);

			if(!$cObj){
				throw new Exception('Could not load Default page.');
			}

			$controller = [ $cObj, $this->configs->router->default->method, [] ];

		}

		// Is it a custom URL defined in the router configs?
		if(empty($controller)
			&& isset($this->configs->router->customRules->{$uri[0]})){
				$controller = $this->tryCustomRulesCall($uri);
		}

		// Is it a REST resource?
		if(empty($controller)
			&& isset($this->configs->router->restResources->{$uri[0]})){
				$controller = $this->tryRestResourceCall($uri);
		}

		// If no luck yet; try using the first two URI elelemnts as a
		// controller/method combinition.
		if(empty($controller)){
			$controller = $this->trySimpleControllerCall($uri);
		}

		// If a controller is found call it; or call the Not Found Page
		if($controller){ 

			call_user_func_array([$controller[0], $controller[1]], $controller[2]);			

		}else{

			$cObj = $this->instantiateClass(
						$this->configs->router->notFound->controller, 
						$this->configs->router->notFound->method
					);

			if(!$cObj){
				throw new Exception('Could not load Not Found page.');
			}

			// Pass the $uri array to it as an argument.
			call_user_func_array([$cObj, $this->configs->router->notFound->method], [$uri]);
		}

	}

	/**
	* Tries to fetch the ControllerClass->method combination for the 
	* RESTful resource.
	*/
	public function tryRestResourceCall($uri){

		$c = $this->configs->router->restResources->{$uri[0]};
		$m = SymfonyRequest::get()->object->getMethod();

		$httpMethodIssues = strpbrk($m, "#$%^&*()+=[]';,./{}|:<>?~");
		$m = (!$httpMethodIssues) ? strtolower($m) . 'Resource' : null;

		$cObj = $this->instantiateClass($c, $m);

		$args = []; 

		foreach ($uri as $key => $value) {
			if($key > 0){
				$args[] = $value;
			}
		}

		return ($cObj) ? [$cObj, $m, $args] : null;

	}

	/**
	* Tries to fetch the ControllerClass->method combination for the
	* custom URI provided by the user. 
	*/
	public function tryCustomRulesCall($uri){

		$customRule = $this->configs->router->customRules->{$uri[0]};

		$cObj = $this->instantiateClass($customRule->controller, $customRule->method);

		$args = []; 

		foreach ($uri as $key => $value) {
			if($key > 0){
				$args[] = $value;
			}
		}

		return 	($cObj) ? [$cObj, $customRule->method, $args] : null;
	}

	/**
	* Tries to fetch the ConrtollerClass->method combination based on
	* the first two URI elements determined by this::getUri() method.
	*/
	public function trySimpleControllerCall($uri){

		$class = ucfirst($uri[0]) .'Controller';
		$cObj = $this->instantiateClass($class, $uri[1]);

		$args = []; 

		foreach ($uri as $key => $value) {
			if($key > 1){
				$args[] = $value;
			}
		}

		$method = (empty($uri[1])) ? 'index' : $uri[1];

		return ($cObj) ? array($cObj, $method, $args) : null;
	}

	/**
	* Tries to locate the given class and method and instansiate the class object.
	*/
	private function instantiateClass($class, $method){

		$file = '/'. trim($this->configs->app->appSystemPath, '/') .'/'.
				trim($this->configs->router->controllersDirectory, '/') .'/'.
				$class.'.php';

		if(!@include($file)) {
			return false;
		}

		// Check to see if controller exists
		if(class_exists($class)){

			$cObj = new $class();
			$method = (empty($method)) ? 'index' : $method;

			// Check if method is callable
			if(method_exists($cObj, $method)){
				return $cObj;
			}
		}

		return false;
	}

	/**
	* Checks to see if required configurations are defined.
	*/
	public function areConfigsDefined(){

		if(isset($this->configs->app->appURL)
			&& isset($this->configs->router->controllersDirectory)
			&& isset($this->configs->router->default) 
			&& is_object($this->configs->router->default)
			&& isset($this->configs->router->notFound) 
			&& is_object($this->configs->router->notFound)){

			return true;
		}

		return false;
	}

	/**
	* Returns an array of URI elements that relate to routing 
	* within this app.
	*/
	public function getUri(){

		$rqst = SymfonyRequest::get();
		$host = trim(
					$rqst->object->server->get('HTTP_HOST'), 
					'www.'
					);

		$fullURI = $rqst->object->server->get('REQUEST_URI');

		if(strpos($this->configs->app->appURL, $host) !== 0){
			throw new Exception('The appURL config property does not match the HTTP Host this app is running on.');
		}

		// If this app was not installed in the root directory of
		// the domain, the $appURI would carry the path to where it
		// is running from.
		$appURI = trim(
						substr(
							$this->configs->app->appURL, 
							strlen($host)
						), 
						'/'
					);

		// Get the URI that relates to routing within the app...
		// In other words; the URI without the $appURI defined above.
		$uri = trim(
					substr(
						trim($fullURI, '/'), 
						strlen($appURI)
					), 
					'/'
				);

		// Get rid of the query string, explode path into an array
		// and clean each element before sending to router.
		$pathArr = explode('?', $uri);
		$arr = explode('/', $pathArr[0]);

		if(!is_array($arr)){
			return [];
		}

		foreach ($arr as $k => $value) {
			preg_match('#([A-Za-z1-9_-])+#', $value, $matches);
			$arr[$k] = $matches[0];
		}

		return $arr;
	}

}