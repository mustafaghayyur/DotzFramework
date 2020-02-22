<?php
namespace DotzFramework\Utilities;

use DotzFramework\Core\Dotz;

class FormGeneration{
	
	public function open($name, $method, $action, $attributes = array()){

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
			$html .= self::generateAttributesString($attr);
			$html .= ">\n";	
		}
		
		return $html;
	}

	/**
	 * Generates HTML for a specified $field definition.
	 */
	public function input($name, $label = null, $attributes = []){

		$default = [
			'name' => $name, 
			'class' => $name.'InputField'
		];

		if(strtolower($attributes['type']) === 'submit'){
			$default['value'] = 'submit';
		}

		if(strtolower($attributes['type']) === 'checkbox'){
			$default['value'] = $name;
		}

		$attr = array_merge($default, $attributes);

		$html = '';

		if(!empty($label)){
			$html = '<label for="'.$attr['name'].'" >'.$label.' </label>';
		}

		$html .= '<input';
		$html .= self::generateAttributesString($attr);
		$html .= " />\n";
		
		return $html;
	}

	public function textarea($name, $initialText = '', $label = null, $attributes =[]){

		$default = [
			'name' => $name, 
			'class' => $name.'TextField',
			'rows'=>'',
			'col'=>''
		];

		$attr = array_merge($default, $attributes);

		$html = '';

		if(!empty($label)){
			$html = '<label for="'.$attr['name'].'" >'.$label.' </label>';
		}

		$html .= '<textarea';
		$html .= self::generateAttributesString($attr);
		$html .= '>';
		$html .= $initialText;
		$html .= "</textarea>\n";

		return $html;
	}


	public function select($name, $options = [], $label = null, $settings = []){

		$default = [
			'name' => $name,
			'class' => $name.'SelectField'
		];

		$selectAttributes = (isset($settings['attr']) 
			&& is_array($settings['attr'])) 
				? $settings['attr']
				: [];

		$attr = array_merge($default, $selectAttributes);

		$html = '';
		$label = (empty($label)) ? '' : $label;

		if(!empty($label)){
			$label = '<label for="'.$attr['name'].'" >'.$label.' </label>';
		}

		$openingTag = '<select';
		$openingTag .= self::generateAttributesString($attr);
		$openingTag .= '>';

		$optionsOutput = '';
		foreach ($options as $key => $text) {
			
			$optionAttributes = ( isset($settings['options'][$key]['attr']) 
				&& is_array($settings['options'][$key]['attr']) ) 
					? $settings['options'][$key]['attr']
					: [];

			$a = array_merge([ 'value' => $key ], $optionAttributes);
			
			if(isset($settings['default']) && $key == $settings['default']){
				$a['selected'] = 'selected';
			}
			
			$option = '<option';
			$option .= self::generateAttributesString($a);
			$option .= ' '. $default .'>';
			$option .= $text;
			$option .= "</option>\n";

			$optionsOutput .= $option;
		}

		$closingTag = '</select>';

		$html = $label ."\n". $openingTag ."\n". $optionsOutput . $closingTag ."\n";

		return $html;
	}

	public function close($data = ''){
		return '</form '. $data .">\n";
	}

	protected static function generateAttributesString($array){
		$str = '';

		foreach ($array as $key => $value) {
			$str .= ' '.$key.'="'. $value .'"';
		}

		return $str;
	}

}