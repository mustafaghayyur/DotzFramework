<?php 

use DotzFramework\Core\Controller;
use DotzFramework\Modules\Form\FilterText;

class TestController extends Controller{

	/**
	 * Home page
	 */
	public function index($test=''){

		$f = new FilterText();

		$f->process();

		//$packet = [ 'msg' => 'Developed by Web Dotz' ];
		//$this->view->load('home', $packet);
	}

	

        
}