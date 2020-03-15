<?php 

use DotzFramework\Core\Controller;
use DotzFramework\Modules\Form;

class TestController extends Controller{

	/**
	 * Home page
	 */
	public function index(){
		
		$packet = [];
		$packet['form'] = new Form\Form();

		$this->view->load('form2', $packet);
	}

	public function submit(){

		$text = $this->input->post('message', false);
		$f = new Form\FilterText();
		
		$packet = [];
		$packet['form'] = new Form\Form();
		$packet['text'] = $f->process('testField', $text);

		$this->view->load('form2', $packet);
	}	

        
}