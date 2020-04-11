<?php 

use DotzFramework\Core\Controller;
use DotzFramework\Modules\Form;
use DotzFramework\Utilities\FileIO;

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
		$packet['text'] = $this->input->secure()->post('text', $filter);

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


	/**
	 * Shows the beauty of the new and improved
	 * FilIO() utility class!
	 */
	public function fileio(){

		// we take a sample file from our documentation
		// READ ONLY! No need to harm the docs!
		$f = new FileIO('./documentation/Views.txt', 'r');

		if($f->ok){
			
			$f->read(); // pointer has reached end of file.
			
			// we want to take a middle sentence...
			$text = $f->seek(450)->read(69);

			// create a new timestamped file...
			$fileName = './FileIOExample-'.time().'.txt';

			// ever had trouble remembering if the + comes before
			// the letter or after? Now it doesn't matter!
			$f2 = new FileIO($fileName, '+w');
			
			if($f2->ok){
				// print above captured $text to screen
				echo $text;
				echo '<br/><br/>';

				// write some things to the time-stamped file.
				echo $f2->seek(0)
						->write("This file will hold some file operation examples:\n\r\n")
						->seek(0, 'end')
						->write('Developed by Web Dotz.')
						->seek(0, 'end of file')
						->write("\n\r\n")
						->seek(0, 'end')
						->write($text)
						->write("\n\r\n")
						->write('Filename: '.$fileName)
						->seek(0, 'beginning')
						->read(); // and output the contents of the file :)
			}
		}

	}
        
}