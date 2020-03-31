<?php 

use DotzFramework\Core\Controller;
use DotzFramework\Modules\Form;

/**
 * This controller shows the power of FilterText and Quill.js.
 * With Quill.js and our filtering library called Filtext(),
 * you can create rich user input interfaces while keeping your
 * app safe from XSS vulnerabilities.
 */
class FilterController extends Controller{
	
	public function index(){
		
		$packet = [];
		$packet['form'] = new Form\Form();
		$packet['text'] = 'enter text here';

		$this->view->load('filter', $packet);
	}

	/**
	 * FilterText implementation:
	 * When you have inputs in your app allowing for HTML code to be supplied,
	 * use FilterText() to filter it, as the below example illustrates.
	 */
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