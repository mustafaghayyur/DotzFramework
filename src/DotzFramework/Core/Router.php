<?php 
namespace DotzFramework\Core;

use \Exception;

class Router {

	/**
	 * Stores application configs.
	 */
	public $configs;

	/**
	 * static property used by ErrorHandler class;
	 * determines wheather to output response
	 * in html or json.
	 */
	public $controllerUsed;

	/**
	* Sets the configs for this router.
	*/
	public function __construct($c = null){
		$this->configs = Dotz::module('configs')->props;
	}

	/**
	* Does the routing.
	*/
	public function do(){

		if(!$this->areConfigsDefined()){
			throw new Exception("[ Router Error ] Router configurations are not correct.");
		}

		$uri = $this->getUri();

		// $controller will hold an array of the controller class,
		// method and an array of arguments to be passed along.
		$controller = null;

		// If there are no uri elements, load the default page.
		if(empty($uri[0])){

			$h = explode('@', $this->configs->router->default);

			$cObj = $this->instantiateClass( $h[1], $h[0] );

			if(!$cObj){
				throw new Exception('[ Router Error ] Could not load Default page.');
			}

			$this->controllerUsed = ($cObj) ? $h[1] : $this->controllerUsed;
			$controller = [ $cObj, $h[0], [] ];

		}

		// Is it a custom URL defined in the router configs?
		if(empty($controller)
			&& isset($this->configs->router->custom->{$uri[0]})){
				$controller = $this->tryCustomRulesCall($uri);
		}

		// Is it a REST resource?
		if(empty($controller)
			&& isset($this->configs->router->rest->{$uri[0]})){
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

			$e = explode('@', $this->configs->router->notFound);

			$cObj = $this->instantiateClass($e[1], $e[0]);

			if(!$cObj){
				throw new Exception('[ Router Error ] Could not load Not Found page.');
			}

			$this->controllerUsed = ($cObj) ? $e[1] : $this->controllerUsed;

			// Pass the $uri array to it as an argument.
			call_user_func_array([$cObj, $e[0]], [$uri]);
		}

	}

	/**
	* Checks to see if required configurations are defined.
	*/
	public function areConfigsDefined(){

		if(isset($this->configs->app->url)
			&& isset($this->configs->app->systemPath)
			&& isset($this->configs->app->controllersDir)
			&& isset($this->configs->router->default) 
			&& isset($this->configs->router->notFound)
		){

			return true;
		}

		return false;
	}

	/**
	* Returns an array of URI elements that relate to routing 
	* within this app.
	*/
	public function getUri(){

		$host = trim(
			Dotz::module('request')->server->get('HTTP_HOST'), 
			'www.'
		);

		$fullURI = Dotz::module('request')->server->get('REQUEST_URI');

		if(strpos($this->configs->app->url, $host) !== 0){
			throw new Exception('[ Router Error ] App URL defined in configs/app.txt does not match the HTTP Host this app is running on.');
		}

		// If this app was not installed in the root directory of
		// the domain, the $appURI would carry the path to where it
		// is running from.
		$appURI = trim(
			substr(
				$this->configs->app->url, 
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
			$arr[$k] = Dotz::grabKey($matches, 0);
		}

		return $arr;
	}

	/**
	* Tries to fetch the ControllerClass->method combination for the 
	* RESTful resource.
	*/
	public function tryRestResourceCall($uri){

		$c = $this->configs->router->rest->{$uri[0]};

		$m = Dotz::module('request')->getMethod();

		$httpMethodIssues = strpbrk($m, "#$%^&*()+=[]';,./{}|:<>?~");
		$m = (!$httpMethodIssues) ? strtolower($m) . 'Resource' : null;

		$cObj = $this->instantiateClass($c, $m);

		$args = []; 

		foreach ($uri as $key => $value) {
			if($key > 0){
				$args[] = $value;
			}
		}

		$this->controllerUsed = ($cObj) ? $c : $this->controllerUsed;
		return ($cObj) ? [$cObj, $m, $args] : null;

	}

	/**
	* Tries to fetch the ControllerClass->method combination for the
	* custom URI provided by the user. 
	*/
	public function tryCustomRulesCall($uri){

		$rule = $this->configs->router->custom->{$uri[0]};
		$c = explode('@', $rule);

		$cObj = $this->instantiateClass($c[1], $c[0]);

		$args = []; 

		foreach ($uri as $key => $value) {
			if($key > 0){
				$args[] = $value;
			}
		}

		$this->controllerUsed = ($cObj) ? $c[1] : $this->controllerUsed;
		return 	($cObj) ? [$cObj, $c[0], $args] : null;
	}

	/**
	* Tries to fetch the ConrtollerClass->method combination based on
	* the first two URI elements determined by this::getUri() method.
	*/
	public function trySimpleControllerCall($uri){

		$class = ucfirst($uri[0]) .'Controller';
		$cObj = $this->instantiateClass($class, Dotz::grabKey($uri, 1));

		$args = []; 

		foreach ($uri as $key => $value) {
			if($key > 1){
				$args[] = $value;
			}
		}

		$method = (empty($uri[1])) ? 'index' : $uri[1];

		$this->controllerUsed = ($cObj) ? $class : $this->controllerUsed;
		return ($cObj) ? array($cObj, $method, $args) : null;
	}

	/**
	* Tries to locate the given class and method and instansiate the class object.
	*/
	private function instantiateClass($class, $method){

		$file = '/'. trim($this->configs->app->systemPath, '/') .'/'.
				trim($this->configs->app->controllersDir, '/') .'/'.
				$class.'.php';

		if(file_exists($file)){
			
			if(!include_once($file)) {
				throw new Exception('[ Router Error ] Could not load Controller file: '.$class.'.php when file exists.');
			}

		}else{
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
	 * Profiler for the app.
	 */
	public function profiler($status = 'off', $m1 = null, $t1 = null){
		
		if($status == 'on'){
			$m2 = memory_get_usage();
			$t2 = microtime(true);
			$m = ($m2 - $m1) / 1000;
			$t = $t2 - $t1;

			echo '<div class="profiler">'.
					'Time: ' . round($t, 3) .'s<br/>'.
					'Memory: ' . round($m, 3) .'kb'.
				'</div>';
		}
	}

}