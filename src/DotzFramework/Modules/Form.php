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
	public function getOpen($name, $method, $action, $attributes = array()){

		$default = [
			'name' => $name, 
			'id' => $name.'Form',
			'class' => $name.'Form',
			'method' => $method, 
			'action' => $action
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
	public function getTextfield($name, $label = null, $attributes = []){
		
		$attributes['type'] = 'text';
		return $this->input($name, $label, $attributes);
	}

	/**
	 * Wrapper function to get the input field type="checkbox"
	 */
	public function getCheckbox($name, $label = null, $attributes = []){
		
		$attributes['type'] = 'checkbox';
		$attributes['value'] = isset($attributes['value']) ? $attributes['value'] : $name;

		if(isset($this->data[$name]) && $this->data[$name] == $name){
			$attributes['checked'] = 'checked';
		}

		return $this->input($name, $label, $attributes);
	}

	/**
	 * Wrapper function to get the input field type="radio"
	 */
	public function getRadiobutton($name, $value, $label = null, $attributes = []){
		
		$attributes['type'] = 'radio';
		$attributes['value'] = $value;

		if(isset($this->data[$name]) && $this->data[$name] == $value){
			$attributes['checked'] = 'checked';
		}

		return $this->input($name, $label, $attributes);
	}

	/**
	 * Wrapper function to get the input field type="button"
	 */
	public function getButton($name, $value = 'Submit', $label = null, $attributes = []){
		
		$attributes['type'] = (isset($attributes['type'])) ? $attributes['type'] : 'submit';
		$attributes['value'] = $value;

		return $this->input($name, $label, $attributes);
	}

	/**
	 * Generates HTML for a specified input field.
	 */
	public function input($name, $label = null, $attributes = []){

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
	public function getTextarea($name, $label = null, $attributes =[]){

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
	public function getSelect($name, $options = [], $label = null, $selectedKey = null, $settings = []){

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
		echo $this->getOpen($name, $method, $action, $attributes);
	}

	public function textfield($name, $label = null, $attributes = []){
		echo $this->getTextfield($name, $label, $attributes);
	}

	public function checkbox($name, $label = null, $attributes = []){
		echo $this->getCheckbox($name, $label, $attributes);
	}

	public function radiobutton($name, $value, $label = null, $attributes = []){
		echo $this->getRadiobutton($name, $value, $label, $attributes);
	}

	public function button($name, $value = 'Submit', $label = null, $attributes = []){
		echo $this->getButton($name, $value, $label, $attributes);
	}	

	public function textarea($name, $label = null, $attributes =[]){
		echo $this->getTextarea($name, $label, $attributes);
	}

	public function select($name, $options = [], $label = null, $defaultOptionKey = null, $settings = []){
		echo $this->getSelect($name, $options, $label, $defaultOptionKey, $settings);
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