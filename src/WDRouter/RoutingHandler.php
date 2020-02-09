<?php 
namespace WDRouter;

use Utilities\SymfonyRequest;
use \Exception;
use Utilities\FileIO;

class RoutingHandler {

	/**
	 * Store the controller/method/arguments
	 * in this property incase the app requires this info
	 * somewhere else.
	 */
	public $controllerCall;

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

		// Initiate variable - $controller_call will hold the final 
		// $controller->$method and their $arguments to be called by the router
		$controller = null;
                
		if(isset($this->configs->router->restResources->{$uri[0]}) && empty($controller)){
			// It's a REST resource...
			$controller = $this->checkRestResource($uri);
		}
                
		if(isset($this->configs->router->customRules->{$uri[0]}) && empty($controller)){
			// It's a custom URL defined in the router configs.
			$controller = $this->checkCustomRules($uri);
		}

		if($this->configs->router->controllerBased && empty($controller)){
			// None of the above. 
			// Try loading a controller/method combination for the provided URI
			$controller = $this->checkControllerCall($uri);
		}
                
		if($controller){ 
			// A controller/method was found!

			// Call the controller
			$cObj = new $controller[0];
                        
			// Set the arguments
			$args = (empty($controller[2])) ? array() : $controller[2];

			// Store in internal memory:
			$this->controllerCall = $controller;

			// Call the method/page!
			call_user_func_array(array($cObj, $controller[1]), $args);			
		
		}else{
			// Could not find an appropriate controller/method combination. 
			// Load error page, as defined in the configs
			$cObj = $this->checkClassAndMethodCallable($this->configs->router->NotFoundController, $this->configs->router->NotFoundMethod);


			// Call the Not Found controller, and pass the $uri to it as an argument.
			call_user_func_array([$cObj, $this->configs->router->NotFoundMethod], [$uri]);
		}

	}

    /**
     * Tries to fetch the Conrtoller->method combination for the URI provided 
     * by the client/user.
     */
	public function checkControllerCall($uri){
		$res = $this->checkClassAndMethodCallable($uri[0], $uri[1]);
                
		if(count($uri) > 2){
			$args = $uri;
			unset($args[0]);
			unset($args[1]);
			$args = array_values($args);
		}

		return ($res) ? array($uri[0], $uri[1], $args) : null;
	}

	/**
	 * Tries to fetch the Controller->method combination for the custom URI provided 
	 * by the user. This method refenernces the config settings defined in json.txt, 
	 * and see if an appropriate controller & method have been defined for this custom uri.
	 */
	public function checkCustomRules($uri){
		
		$res = $this->checkClassAndMethodCallable($this->configs->router->customRules->{$uri[0]}->controller, $this->configs->router->customRules->{$uri[0]}->method);
                
        $specialchrs = strpbrk($uri[0], "#$%^&*()+=[]';,./{}|:<>?~");
        
        if(count($uri) > 1){
            $args = $uri;
            unset($args[0]);
            $args = array_values($args);
        }
                
		return ($res && !$specialchrs) ? array($this->configs->router->customRules->{$uri[0]}->controller, $this->configs->router->customRules->{$uri[0]}->method, $args) : null;
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

		$httpmethodOk = strpbrk($_SERVER['REQUEST_METHOD'], "#$%^&*()+=[]';,./{}|:<>?~");
		$m = (!$httpmethodOk) ? strtolower($_SERVER['REQUEST_METHOD'].'_resource') : null;

		$res = $this->checkClassAndMethodCallable($c, $m);

		$specialchrs = strpbrk($uri[0], "#$%^&*()+=[]';,./{}|:<>?~");

		if(count($uri) > 1){
		    $args = $uri;
		    unset($args[0]);
		    $args = array_values($args);
		}
                
		return ($res && !$specialchrs) ? array($c, $m, $args) : null;
	
	}

	/**
	 * A helper function. Should be in the helpers.php file.
	 */
	private function checkClassAndMethodCallable($class, $method){
		
		if(!@include($this->configs->router->controllersDirectory.'/'.$class.".php")) return false;

		// Check to see if controller exists
		if(class_exists($class)){
			// Check if method is callable
			$t = new $class();
			if(method_exists($t, $method)){
                return $t;
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
	            && isset($this->configs->router->controllerBased)
	            && isset($this->configs->router->NotFoundController)
	           	&& isset($this->configs->router->NotFoundMethod)){
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
	    
	    if(strpos($this->configs->router->appURL, $host) !== 0){
	        return false;
	    }
	    
	    // We want to exclude this from the final array returned by this function...
	    $appURI = trim(substr($this->configs->router->appURL, strlen($host)), '/');
	    
	    // Get the URI that relates to routing within the app...
	    $uri = trim(
		    		substr(
		    			trim($fullURI, '/'), 
		    			strlen($appURI)
		    		), 
	    		'/');
	    
	    $arr = explode('/', $uri);
	    
	    foreach ($arr as $k => $value) {
	        preg_match('#([A-Za-z1-9_-])+#', $value, $matches);
	        $arr[$k] = $matches[0];
	    }
	    
	    return $arr;
	}

}