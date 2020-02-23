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
	public function getOpen($attributes = []){

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
	public function getTextfield($attributes = []){
		
		$attributes['type'] = 'text';
		return $this->input($attributes);
	}

	/**
	 * Wrapper function to get the input field type="checkbox"
	 */
	public function getCheckbox($attributes = []){
		
		$name = $attributes['name'];
		$attributes['type'] = 'checkbox';
		$attributes['value'] = isset($attributes['value']) ? $attributes['value'] : $attributes['name'];

		if(isset($this->data[$name]) && $this->data[$name] == $attributes['name']){
			$attributes['checked'] = 'checked';
		}

		return $this->input($attributes);
	}

	/**
	 * Wrapper function to get the input field type="radio"
	 */
	public function getRadiobutton($attributes = []){

		$name = $attributes['name'];
		$attributes['type'] = 'radio';

		if(isset($this->data[$name]) && $this->data[$name] == $attributes['value']){
			$attributes['checked'] = 'checked';
		}

		return $this->input($attributes);
	}

	/**
	 * Wrapper function to get the input field type="button"
	 */
	public function getButton($attributes = []){
		
		$attributes['type'] = (isset($attributes['type'])) ? $attributes['type'] : 'submit';
		$attributes['value'] = (isset($attributes['value'])) ? $attributes['value'] : 'Submit';

		return $this->input($attributes);
	}

	/**
	 * Generates HTML for a specified input field.
	 */
	public function input($attributes = []){

		$name = $attributes['name'];

		$default = [
			'name' => $name, 
			'class' => $name.'InputField'
		];

		if(isset($this->data[$name])){
			$default['value'] = $this->data[$name];
		}

		$attr = array_merge($default, $attributes);

		$html = '';

		if(!empty($attributes['label'])){
			$html = '<label for="'.$attr['name'].'" >'.$attributes['label'].' </label>';
		}

		$html .= '<input';
		$html .= self::attributesToString($attr);
		$html .= " />\n";
		
		return $html;
	}

	/**
	 * Generates the html for a textarea field
	 */
	public function getTextarea($attributes =[]){

		$name = $attributes['name'];

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

		if(!empty($attributes['label'])){
			$html = '<label for="'.$attr['name'].'" >'.$attributes['label'].' </label>';
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
	public function getSelect($settings = []){

		$name = $settings['name'];

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
			$settings['selected'] = $this->data[$name];
		}else{
			$settings['selected'] = isset($settings['default']) ? $settings['default'] : null;
		}

		$html = '';
		$label = '';

		if(!empty($attributes['label'])){
			$label = '<label for="'.$attr['name'].'" >'.$settings['label'].' </label>';
		}

		$openingTag = '<select';
		$openingTag .= self::attributesToString($attr);
		$openingTag .= '>';

		$optionsOutput = '';

		$settings['options'] = ( isset($settings['options']) && is_array($settings['options']) )
					? $settings['options'] 
					: [];

		foreach ($settings['options'] as $key => $text) {
			
			$additionalAttributes = ( isset($settings['options_attr'][$key]) && is_array($settings['options_attr'][$key]) ) 
						? $settings['options_attr'][$key]
						: [];

			$optionAttributes = array_merge([ 'value' => $key ], $additionalAttributes);

			if($key === $settings['selected']){
				$optionAttributes['selected'] = 'selected';
			}
			
			$option = '<option';
			$option .= self::attributesToString($optionAttributes);
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
	
	//public function open($name, $method, $action, $attributes = array()){
	public function open($name){
		return new Element($name, $this, 'getOpen');
	}

	public function textfield($name){
		return new Element($name, $this, 'getTextfield');
	}

	public function checkbox($name){
		return new Element($name, $this, 'getCheckbox');
	}

	public function radiobutton($name){
		return new Element($name, $this, 'getRadiobutton');
	}

	public function button($name){
		return new Element($name, $this, 'getButton');
	}	

	public function textarea($name){
		return new Element($name, $this, 'getTextarea');
	}

	public function select($name){
		return new Element($name, $this, 'getSelect');
	}

	public function close(){
		return new Element(null, $this, 'getClose');
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

	public $callback;

	public function __construct($n, $form, $funcName){
		$this->obj = [ 'name' => $n ];
		$this->form = $form;
		$this->callback = $funcName;
	}

	public function show(){
		echo $this->form->{$this->callback}((array)$this->obj);
	}

	public function get(){
		return $this->form->{$this->callback}((array)$this->obj);
	}

	public function method($m){
		$this->obj['method'] = $m;
		return $this;
	}

	public function action($a){
		$this->obj['action'] = $a;
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

	public function data($d){
		$this->obj['data'] = $d;
		return $this;
	}

}