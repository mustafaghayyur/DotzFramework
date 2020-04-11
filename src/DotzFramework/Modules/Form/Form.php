<?php
namespace DotzFramework\Modules\Form;

use DotzFramework\Core\Dotz;
use DotzFramework\Utilities\CSRF;

class Form {

	/**
	 * Holds data to bind with the form.
	 */
	public $data;

	public $jwt;

	public function __construct(){
		$c = Dotz::config('app');
		$this->jwt = null;

		if($c->csrf->check === true || $c->csrf->check === 'true'){
			if($c->csrf->tokenization === true || $c->csrf->tokenization === 'true'){
				$this->jwt = $this->hidden('jwt')->value(CSRF::generateToken())->get();
			}
		}
	}

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
		return new Element($name, 'getOpen', null, $this->jwt);
	}

	public function textfield($name){
		return new Element($name, 'getTextfield', Dotz::grabKey($this->data, $name));
	}

	public function hidden($name){
		return new Element($name, 'getHiddenField', Dotz::grabKey($this->data, $name));
	}

	public function checkbox($name){
		return new Element($name, 'getCheckbox', Dotz::grabKey($this->data, $name));
	}

	public function radiobutton($name){
		return new Element($name, 'getRadiobutton', Dotz::grabKey($this->data, $name));
	}

	public function button($name){
		return new Element($name, 'getButton', Dotz::grabKey($this->data, $name));
	}	

	public function textarea($name){
		return new Element($name, 'getTextarea', Dotz::grabKey($this->data, $name));
	}

	public function password($name){
		return new Element($name, 'getPassword', null);
	}

	public function editor($name){
		$editor = $this->hidden($name)->value('')->get();
		return new Element($name, 'getWYSIWYG', Dotz::grabKey($this->data, $name), $editor);
	}

	public function select($name){
		return new Element($name, 'getSelect', Dotz::grabKey($this->data, $name));
	}

	public function close(){
		return new Element(null, 'getClose');
	}

}
