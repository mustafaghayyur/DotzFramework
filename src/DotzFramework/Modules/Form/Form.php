<?php
namespace DotzFramework\Modules\Form;

use DotzFramework\Modules\Form\Element;

class Form {

	/**
	 * Holds data to bind with the form.
	 */
	public $data;

	/**
	 * Binds given data to this instance of Form(). 
	 * All provided values passed correctly will be populated
	 * in the generated form.
	 */
	public function bind($data){
		
		// Unset the data for recurring calls to this method.
		$this->data = [];

		if(is_array($data)){
			$this->data = $data;
		}else{
			$this->data = (array)$data;
		}
	}

	public function open($name){
		return new Element($name, 'getOpen');
	}

	public function textfield($name){
		return new Element($name, 'getTextfield', $this->data[$name]);
	}

	public function checkbox($name){
		return new Element($name, 'getCheckbox', $this->data[$name]);
	}

	public function radiobutton($name){
		return new Element($name, 'getRadiobutton', $this->data[$name]);
	}

	public function button($name){
		return new Element($name, 'getButton', $this->data[$name]);
	}	

	public function textarea($name){
		return new Element($name, 'getTextarea', $this->data[$name]);
	}

	public function select($name){
		return new Element($name, 'getSelect', $this->data[$name]);
	}

	public function close(){
		return new Element(null, 'getClose');
	}

}
