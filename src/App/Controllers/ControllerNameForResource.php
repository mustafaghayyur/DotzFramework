<?php 
use DotzFramework\Core\Controller;

class ControllerNameForResource extends Controller {

	public function getResource($arg1 = null, $arg2 = null, $arg3 = null){
		$this->view->json(['msg'=>'This is a get request.'. $arg1]);
	}
        
    public function postResource(){
		$this->view->json(['msg'=>'This is a post request.']);
	}
        
    public function putResource(){
		$this->view->json(['msg'=>'This is a put request.']);
	}
        
    public function deleteResource(){
		$this->view->json(['msg'=>'This is a delete request.']);
	}
        
}