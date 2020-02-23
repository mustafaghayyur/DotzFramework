<?php
namespace DotzFramework\Modules;

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

	/**
	 * Generates the opening html form tag.
	 */
	//public function getOpen($name, $method, $action, $attributes = array()){
	public function getOpen($attributes){

		$default = [
			'name' => $attributes['name'], 
			'id' => $attributes['name'].'Form',
			'class' => $attributes['name'].'Form',
			'method' => '', 
			'action' => ''
		];

		$attr = array_merge($default, $attributes);

		if(is_array($attr) && isset($attr)){
			$html = '<form';
			$html .= self::attributesToString($attr);
			$html .= ">\n";	
		}
		
		return $html;
	}

	/**
	 * Wrapper function to get the input field type="text"
	 */
	public function getTextfield($label = null, $name, $attributes = []){
		
		$attributes['type'] = 'text';
		return $this->input($label, $name, $attributes);
	}

	/**
	 * Wrapper function to get the input field type="checkbox"
	 */
	public function getCheckbox($label = null, $name, $attributes = []){
		
		$attributes['type'] = 'checkbox';
		$attributes['value'] = isset($attributes['value']) ? $attributes['value'] : $name;

		if(isset($this->data[$name]) && $this->data[$name] == $name){
			$attributes['checked'] = 'checked';
		}

		return $this->input($label, $name, $attributes);
	}

	/**
	 * Wrapper function to get the input field type="radio"
	 */
	public function getRadiobutton($label = null, $name, $value, $attributes = []){
		
		$attributes['type'] = 'radio';
		$attributes['value'] = $value;

		if(isset($this->data[$name]) && $this->data[$name] == $value){
			$attributes['checked'] = 'checked';
		}

		return $this->input($label, $name, $attributes);
	}

	/**
	 * Wrapper function to get the input field type="button"
	 */
	public function getButton($name, $value = 'Submit', $label = null, $attributes = []){
		
		$attributes['type'] = (isset($attributes['type'])) ? $attributes['type'] : 'submit';
		$attributes['value'] = $value;

		return $this->input($label, $name, $attributes);
	}

	/**
	 * Generates HTML for a specified input field.
	 */
	public function input($label = null, $name, $attributes = []){

		$default = [
			'name' => $name, 
			'class' => $name.'InputField'
		];

		if(isset($this->data[$name])){
			$default['value'] = $this->data[$name];
		}

		$attr = array_merge($default, $attributes);

		$html = '';

		if(!empty($label)){
			$html = '<label for="'.$attr['name'].'" >'.$label.' </label>';
		}

		$html .= '<input';
		$html .= self::attributesToString($attr);
		$html .= " />\n";
		
		return $html;
	}

	/**
	 * Generates the html for a textarea field
	 */
	public function getTextarea($label = null, $name, $attributes =[]){

		$default = [
			'name' => $name, 
			'class' => $name.'TextField',
			'rows'=>'',
			'col'=>''
		];

		$attr = array_merge($default, $attributes);

		$initialText = '';
		if(isset($this->data[$name])){
			$initialText = $this->data[$name];
		}

		$html = '';

		if(!empty($label)){
			$html = '<label for="'.$attr['name'].'" >'.$label.' </label>';
		}

		$html .= '<textarea';
		$html .= self::attributesToString($attr);
		$html .= '>';
		$html .= $initialText;
		$html .= "</textarea>\n";

		return $html;
	}

	/**
	 * Generates the HTML for a slect input field
	 */
	public function getSelect($label = null, $name, $options = [], $selectedKey = null, $settings = []){

		$default = [
			'name' => $name,
			'class' => $name.'SelectField'
		];

		$selectAttributes = (isset($settings['attr']) 
			&& is_array($settings['attr'])) 
				? $settings['attr']
				: [];

		$attr = array_merge($default, $selectAttributes);

		if(isset($this->data[$name])){
			$selectedKey = $this->data[$name];
		}

		$html = '';
		$label = (empty($label)) ? '' : $label;

		if(!empty($label)){
			$label = '<label for="'.$attr['name'].'" >'.$label.' </label>';
		}

		$openingTag = '<select';
		$openingTag .= self::attributesToString($attr);
		$openingTag .= '>';

		$optionsOutput = '';
		foreach ($options as $key => $text) {
			
			$optionAttributes = ( isset($settings['options'][$key]['attr']) 
				&& is_array($settings['options'][$key]['attr']) ) 
					? $settings['options'][$key]['attr']
					: [];

			$a = array_merge([ 'value' => $key ], $optionAttributes);

			if($key === $selectedKey){
				$a['selected'] = 'selected';
			}
			
			$option = '<option';
			$option .= self::attributesToString($a);
			$option .= '>';
			$option .= $text;
			$option .= "</option>\n";

			$optionsOutput .= $option;
		}

		$closingTag = '</select>';

		$html = $label ."\n". $openingTag ."\n". $optionsOutput . $closingTag ."\n";

		return $html;
	}

	/**
	 * Generates the closing tag for a html form.
	 */
	public function getClose($data = ''){
		return '</form '. $data .">\n";
	}

	/**
	 * The following few methods are wrapper functions to allow for
	 * quick printing of the HTML form elements onto the screen.
	 */
	
	public function open($name, $method, $action, $attributes = array()){
		$e = new Element($this);
		return $e->name($name, $this, 'getOpen');
		//echo $this->getOpen($name, $method, $action, $attributes);
	}

	public function textfield($label = null, $name, $attributes = []){
		echo $this->getTextfield($label, $name, $attributes);
	}

	public function checkbox($label = null, $name, $attributes = []){
		echo $this->getCheckbox($label, $name, $attributes);
	}

	public function radiobutton($label = null, $name, $value, $attributes = []){
		echo $this->getRadiobutton($label, $name, $value, $attributes);
	}

	public function button($name, $value = 'Submit', $label = null, $attributes = []){
		echo $this->getButton($name, $value, $label, $attributes);
	}	

	public function textarea($label = null, $name, $attributes =[]){
		echo $this->getTextarea($label, $name, $attributes);
	}

	public function select($label = null, $name, $options = [], $defaultOptionKey = null, $settings = []){
		echo $this->getSelect($label, $name, $options, $defaultOptionKey, $settings);
	}

	public function close($data = ''){
		echo $this->getClose($data);
	}

	/**
	 * Util to generate quick key-value pairs for element attributes
	 */
	protected static function attributesToString($array){
		$str = '';

		foreach ($array as $key => $value) {
			$str .= ' '.$key.'="'. $value .'"';
		}

		return $str;
	}

}

class Element {

	public $obj;

	public $requiredParams;

	public $form;

	public $function;

	public function __construct($arr, $form, $func){
		$this->obj = [];
		$this->form = $form;
	}

	protected function show(){
		echo $this->form->{$this->callback}((array)$this->obj);
	}

	public function name($n){
		$this->obj['name'] = $n;
		return $this;
	}

	public function value($v){
		$this->obj['value'] = $v;
		return $this;
	}

	public function label($l){
		$this->obj['label'] = $l;
		return $this;
	}

	public function type($t){
		$this->obj['type'] = $t;
		return $this;
	}

	public function options($o){
		$this->obj['options'] = $o;
		return $this;
	}

	public function option($key, $value){
		$this->obj['options'][$key] = $value;
		return $this;
	}

	public function default($k){
		$this->obj['default'] = $k;
		return $this;
	}

}