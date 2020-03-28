<?php 

use DotzFramework\Core\Controller;
use DotzFramework\Modules\Form;

class FilterController extends Controller{
	
	public function index(){
		
		$packet = [];
		$packet['form'] = new Form\Form();

		$this->view->load('filter', $packet);
	}

	public function submit(){

		$filter = new Form\FilterText();
		$filter->addAllowedTags(['svg']);

		$packet = [];
		$packet['form'] = new Form\Form();
		$packet['text'] = $this->input->post('text', $filter);

		$this->view->load('filter', $packet);
	}

	/**
	 * Try out the powerful Quill JS editor
	 * https://quilljs.com/
	 * Form returns the filtered output.
	 */
	public function editor(){
		
		$packet = [];
		$packet['form'] = new Form\Form();

		$this->view->load('wysiwyg', $packet);
	}

        
}