<?php
namespace DotzFramework\Modules\Form;

class FormGenerator {

	/**
	 * Generates the opening html form tag.
	 */
	public static function getOpen($attributes = []){

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
	public static function getTextfield($attributes = []){
		
		$attributes['type'] = 'text';
		return self::getInput($attributes);
	}

	/**
	 * Wrapper function to get the input field type="checkbox"
	 */
	public static function getCheckbox($attributes = []){
		
		$name = $attributes['name'];
		$attributes['type'] = 'checkbox';
		$attributes['value'] = isset($attributes['value']) ? $attributes['value'] : $attributes['name'];

		if(isset($attributes['systemBoundValue']) && $attributes['systemBoundValue'] == $attributes['name']){
			$attributes['checked'] = 'checked';
		}

		return self::getInput($attributes);
	}

	/**
	 * Wrapper function to get the input field type="radio"
	 */
	public static function getRadiobutton($attributes = []){

		$name = $attributes['name'];
		$attributes['type'] = 'radio';

		if(isset($attributes['systemBoundValue']) && $attributes['systemBoundValue'] == $attributes['value']){
			$attributes['checked'] = 'checked';
		}

		return self::getInput($attributes);
	}

	/**
	 * Wrapper function to get the input field type="button"
	 */
	public static function getButton($attributes = []){
		
		$attributes['type'] = (isset($attributes['type'])) ? $attributes['type'] : 'submit';
		$attributes['value'] = (isset($attributes['value'])) ? $attributes['value'] : 'Submit';

		return self::getInput($attributes);
	}

	/**
	 * Generates HTML for a specified input field.
	 */
	public static function getInput($attributes = []){

		$name = $attributes['name'];

		$default = [
			'name' => $name, 
			'class' => $name.'InputField'
		];

		$attr = array_merge($default, $attributes);

		if(isset($attributes['systemBoundValue'])){
			$attr['value'] = $attributes['systemBoundValue'];
		}
		
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
	public static function getTextarea($attributes =[]){

		$name = $attributes['name'];

		$default = [
			'name' => $name, 
			'class' => $name.'TextField',
			'rows'=>'',
			'col'=>''
		];

		$attr = array_merge($default, $attributes);

		$initialText = '';
		if(isset($attributes['systemBoundValue'])){
			$initialText = $attributes['systemBoundValue'];
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
	public static function getSelect($settings = []){

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

		if(isset($settings['systemBoundValue'])){
			$settings['selected'] = $settings['systemBoundValue'];
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
	public static function getClose($attributes){
		return '</form '. $attributes['data'] .">\n";
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