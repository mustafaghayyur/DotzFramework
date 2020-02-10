<?php 
namespace DotzFramework\Core;

use DotzFramework\Utilities\SymfonyRequest;
use \Exception;
use DotzFramework\Utilities\FileIO;

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

		if(!$this->checkRouterSettings()){
			throw new Exception("Router configurations are not correct.");
		}

		$uri = $this->getUri();

		// $controller will hold an array of the controller class,
		// controller method and an array of arguments to be passsed
		// to the controller method.
		$controller = null;

		// If there are no uri elements, load the default page.
		if(is_array($uri) && empty($uri[0])){
			
			$cObj = $this->checkClassAndMethodCallable(
				$this->configs->router->default->controller, 
				$this->configs->router->default->method
			);

			$controller = [ $cObj, 
							$this->configs->router->default->method, 
							[] ];

		}

		// Is it a REST resource?
		if(isset($this->configs->router->restResources->{$uri[0]}) && empty($controller)){
			$controller = $this->checkRestResource($uri);
		}
                
		// Is it a custom URL defined in the router configs?
		if(isset($this->configs->router->customRules->{$uri[0]}) && empty($controller)){
			$controller = $this->checkCustomRules($uri);
		}

		// If no luck yet; try using the first two URI elelemnts as a
		// controller/method combinition.
		if(empty($controller)){
			$controller = $this->checkControllerCall($uri);
		}

		// If a controller is found call it, or call the Not Found Page
		if($controller){ 
			
			// Call the method/page!
			call_user_func_array([$controller[0], $controller[1]], $controller[2]);			
		
		}else{

			// Load error page, as defined in the configs
			$cObj = $this->checkClassAndMethodCallable(
				$this->configs->router->notFound->controller, 
				$this->configs->router->notFound->method
			);


			// Pass the $uri to it as an argument.
			call_user_func_array([$cObj, $this->configs->router->notFound->method], [$uri]);
		}

	}

    /**
     * Tries to fetch the Conrtoller->method combination for the URI provided 
     * by the client/user.
     */
	public function checkControllerCall($uri){
		
		$class = ucfirst($uri[0]) .'Controller';
		$cObj = $this->checkClassAndMethodCallable($class, $uri[1]);
		
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
	 * Tries to fetch the Controller->method combination for the custom URI provided 
	 * by the user. This method refenernces the config settings defined in json.txt, 
	 * and see if an appropriate controller & method have been defined for this custom uri.
	 */
	public function checkCustomRules($uri){
		
		$customRule = $this->configs->router->customRules->{$uri[0]};

		$cObj = $this->checkClassAndMethodCallable($customRule->controller, $customRule->method);

        $args = []; 

		foreach ($uri as $key => $value) {
			if($key > 0){
				$args[] = $value;
			}
		}

		return 	($cObj) ? 
					[$cObj, $customRule->method, $args] : 
					null;
	}

	/**
	 * Tries to fetch the Controller->method combination for the RESTful 
	 * resource the library has identified based on the URI provided by the user.
	 * If the conrtoller for this URI has been defined in the json.txt file, an 
	 * appropriate {http_method}_resource() function will be called. If this 
	 * method exists.
	 */
	public function checkRestResource($uri){

		$c = $this->configs->router->restResources->{$uri[0]};
		$m = SymfonyRequest::get()->object->getMethod();
		
		$httpMethodIssues = strpbrk($m, "#$%^&*()+=[]';,./{}|:<>?~");
		$m = (!$httpMethodIssues) ? strtolower($m.'_resource') : null;

		$cObj = $this->checkClassAndMethodCallable($c, $m);
					
		$args = []; 

		foreach ($uri as $key => $value) {
			if($key > 0){
				$args[] = $value;
			}
		}
		
		return ($cObj) ? [$cObj, $m, $args] : null;
	
	}

	/**
	 * A helper function. Should be in the helpers.php file.
	 */
	private function checkClassAndMethodCallable($class, $method){

		$file = $this->configs->app->appSystemPath .'/'. 
				$this->configs->router->controllersDirectory .'/'.
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
	 * Checks to see if esssential settings are defined in json.txt
	 */
	public function checkRouterSettings(){
	    
	    if(isset($this->configs->app->appURL)
	            && isset($this->configs->router->controllersDirectory)
	           	&& isset($this->configs->router->notFound) 
	           	&& is_object($this->configs->router->notFound)){
	        return true;
	    }
	}

	/**
	 * Determine the URI elelments that relate to a path in the App as defined by
	 * configuration settings. Also filters for non-safe characters in the uri.
	 *
	 * Returns an array of URI elements that relate to routing within this app.
	 */
	public function getUri(){
	    
	    $rqst = SymfonyRequest::get();
	    $host = trim(
			    	$rqst->object->server->get('HTTP_HOST'), 
			    	'www.'
			    );
	    
	    $fullURI = $rqst->object->server->get('REQUEST_URI');
	    
	    if(strpos($this->configs->app->appURL, $host) !== 0){
	        throw new Exception('The appURL property was not configured correctly for this app.');
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
	    		'/');
	    
	    $pathArr = explode('?', $uri);
	    $arr = explode('/', $pathArr[0]);
	    
	    foreach ($arr as $k => $value) {
	        preg_match('#([A-Za-z1-9_-])+#', $value, $matches);
	        $arr[$k] = $matches[0];
	    }

	    return $arr;
	}

}