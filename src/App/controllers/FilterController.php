<?php 

use DotzFramework\Core\Controller;
use DotzFramework\Modules\Form;

class FilterController extends Controller{

	/**
	 * Home page
	 */
	public function index(){
		
		$packet = [];
		$packet['form'] = new Form\Form();

		$this->view->load('filter', $packet);
	}

	public function submit(){

		$filter = new Form\FilterText();

		$packet = [];
		$packet['form'] = new Form\Form();
		$packet['text'] = $this->input->post('text', $filter);

		$this->view->load('filter', $packet);
	}	

        
}