<?php 

use DotzFramework\Core\Controller;
use DotzFramework\Modules\Form;

class TestController extends Controller{

	/**
	 * Home page
	 */
	public function index($test=''){

		$packet['form'] = new Form\Form();
		$f = new Form\FilterText();

		$this->view->load('form2', $packet);
	}

	public function submit($test=''){

		$text = $this->input->post('message', false);
		$f = new Form\FilterText();
		$f->process($text);

	}	

        
}