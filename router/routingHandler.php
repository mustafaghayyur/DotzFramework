<?php 
namespace router;

/**
* A project of Web Dotz
**/

require ('helpers.php');

use \Exception;

class RoutingHandler {

	public $uri, $controller_call, $config;

	public function __construct($routerDirectory){
		
                error_reporting(E_ALL & ~E_NOTICE);
                
                $this->config = load_mapper($routerDirectory);

		if(!$this->config){
			throw new Exception("Router configurations could not be loaded.");
		}

		
                $this->uri = get_uri($this->config->appURL, $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']);

		//initial variable
                $this->controller_call = null;
                
		if(isset($this->config->restResources->{$this->uri[0]}) && empty($this->controller_call)){
			//REST resource...
			$this->controller_call = $this->check_rest_resource($this->uri);
		}
                
                if(isset($this->config->customRules->{$this->uri[0]}) && empty($this->controller_call)){
			//custom rule...
			$this->controller_call = $this->check_custom_rules($this->uri);

		}

		if($this->config->controllerBased && empty($this->controller_call)){
			//normal controller based method calls allowed
			$this->controller_call = $this->check_controller_call($this->uri);
		}
                
		if($this->controller_call){

			//Call the controller
			$controller = new $this->controller_call[0];
                        
                        $args = (empty($this->controller_call[2])) ? array() : $this->controller_call[2];
                        call_user_func_array(array($controller, $this->controller_call[1]), $args);			
                        		
		}else{
			include($this->config->ErrorPage);
		}

	}

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

	public function check_rest_resource($uri){
                $c = $this->config->restResources->{$uri[0]};
                $m = strtolower($_SERVER['REQUEST_METHOD'].'_resource');
		
                $res = $this->check_class_and_method_callable($c, $m);
                
                $specialchrs = strpbrk($uri[0], "#$%^&*()+=[]';,./{}|:<>?~");
                
                if(count($uri) > 1){
                    $args = $uri;
                    unset($args[0]);
                    $args = array_values($args);
                }
                
		return ($res && !$specialchrs) ? array($c, $m, $args) : null;
	
	}

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