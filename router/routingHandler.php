<?php 
namespace router;

/**
* A project of Web Dotz
**/

require ('helpers.php');

use \Exception;

class RoutingHandler {

	public $uri, $controller_call, $config;

	/**
         * The contructor has all the logic for loading the appropriate controller.
         * 
         * @param type $routerDirectory -  where the this file is located in reference to the root of the app.
         * @throws Exception
         */
        public function __construct($routerDirectory){
		
                error_reporting(E_ALL & ~E_NOTICE);
                
                $this->config = load_mapper($routerDirectory);

		if(!$this->config){
			throw new Exception("Router configurations could not be loaded.");
		}

		
                $this->uri = get_uri($this->config->appURL, $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']);

		//initiate variable - $controller_call will hold the final $controller->$method and their $arguments to be called by the router
                $this->controller_call = null;
                
		if(isset($this->config->restResources->{$this->uri[0]}) && empty($this->controller_call)){
			//It's a REST resource...
			$this->controller_call = $this->check_rest_resource($this->uri);
		}
                
                if(isset($this->config->customRules->{$this->uri[0]}) && empty($this->controller_call)){
			//It's a custom URL defined in the json.txt file...
			$this->controller_call = $this->check_custom_rules($this->uri);

		}

		if($this->config->controllerBased && empty($this->controller_call)){
			//None of the above. Try loading a controller/method combination for the provided URI
			$this->controller_call = $this->check_controller_call($this->uri);
		}
                
		if($this->controller_call){ //a controller/method was found!

			//Call the controller
			$controller = new $this->controller_call[0];
                        
                        //set the arguments
                        $args = (empty($this->controller_call[2])) ? array() : $this->controller_call[2];
                        
                        //call the method/page!
                        call_user_func_array(array($controller, $this->controller_call[1]), $args);			
                        		
		}else{
                        //could not find an appropriate controller/method combination. Load error page, as defined in json.txt.
                        include($this->config->ErrorPage);
		}

	}

        /**
         * Tries to fetch the Conrtoller->method combination for the URI provided by the client/user.
         * 
         * @param type $uri - array
         * @return type (controller, method, $args) array
         */
	public function check_controller_call($uri){
		$res = $this->check_class_and_method_callable($uri[0], $uri[1]);
                
                if(count($uri) > 2){
                    $args = $uri;
                    unset($args[0]);
                    unset($args[1]);
                    $args = array_values($args);
                }
                
		return ($res) ? array($uri[0], $uri[1], $args) : null;
	}

        /**
         * Tries to fetch the Controller->method combination for the custom URI provided by the user.
         * This method refenernces the config settings defined in json.txt, and see if an appropriate controller & method have been defined for this custom uri.
         * 
         * @param type $uri - array
         * @return type - (controller, method, $args) array
         */
	public function check_custom_rules($uri){
		
		$res = $this->check_class_and_method_callable($this->config->customRules->{$uri[0]}->controller, $this->config->customRules->{$uri[0]}->method);
                
                $specialchrs = strpbrk($uri[0], "#$%^&*()+=[]';,./{}|:<>?~");
                
                if(count($uri) > 1){
                    $args = $uri;
                    unset($args[0]);
                    $args = array_values($args);
                }
                
		return ($res && !$specialchrs) ? array($this->config->customRules->{$uri[0]}->controller, $this->config->customRules->{$uri[0]}->method, $args) : null;
	}

        /**
         * Tries to fetch the Controller->method combination for the RESTful resource the library has identified based on the URI provided by the user.
         * If the conrtoller for this URI has been defined in the json.txt file, an appropriate {http_method}_resource() function will be called. If this method exists.
         * 
         * @param type $uri - array
         * @return type - (controller, method, $args) array
         */
	public function check_rest_resource($uri){
                $c = $this->config->restResources->{$uri[0]};
                
                $httpmethodOk = strpbrk($_SERVER['REQUEST_METHOD'], "#$%^&*()+=[]';,./{}|:<>?~");
                $m = (!$httpmethodOk) ? strtolower($_SERVER['REQUEST_METHOD'].'_resource') : null;
		
                $res = $this->check_class_and_method_callable($c, $m);
                
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
         * 
         * @param type $class
         * @param type $method
         * @return boolean
         */
	private function check_class_and_method_callable($class, $method){
		
                if(!@include($this->config->controllersDirectory.'/'.$class.".php")) return false;
                
                //check to see if controller exists
		if(class_exists($class)){
			//check if method is callable
			$t = new $class();
			if(method_exists($t, $method)){
                                return true;
			}
		}

		return false;
	}

}