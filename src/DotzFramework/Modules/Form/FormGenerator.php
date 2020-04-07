<?php
namespace DotzFramework\Modules\Form;

use DotzFramework\Core\Dotz;

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

		// If a hidden JWT field was created by the system
		// it would be in the $attributes['additional'] 
		$jwt = Dotz::grabKey($attributes, 'additional');
		
		$attr = array_merge($default, $attributes);
		
		unset($attr['additional']);
		unset($attr['systemBoundValue']);

		if(is_array($attr) && isset($attr)){
			$html = '<form';
			$html .= self::attributesToString($attr);
			$html .= ">\n";	
		}

		if(!empty($jwt)){
			$html .= "\n\r\n". $jwt;
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

	public static function getHiddenField($attributes = []){
		
		$attributes['type'] = 'hidden';
		return self::getInput($attributes);
	}

	public static function getPassword($attributes = []){
		
		$attributes['type'] = 'password';
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
		$attributes['class'] = $attributes['name'].'Button button';

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

		unset($attr['systemBoundValue']);
		unset($attr['additional']);
		unset($attr['label']);
		
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
			'class' => $name.'TextField'
		];

		$initialText = '';

		if(isset($attributes['text'])){
			$initialText = $attributes['text'];
		}

		if(isset($attributes['systemBoundValue'])){
			$initialText = $attributes['systemBoundValue'];
		}

		$attr = array_merge($default, $attributes);

		unset($attr['systemBoundValue']);
		unset($attr['additional']);
		unset($attr['label']);
		unset($attr['text']);

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
	 * Generates a WYSIWYG editor.
	 */
	public static function getWYSIWYG($attributes = []){
		
		$label = isset($attributes['label']) ? $attributes['label'] : '';
		$name = isset($attributes['name']) ? $attributes['name'] : '';
		$initialText = isset($attributes['text']) ? $attributes['text'] : '';
		$hidden = isset($attributes['additional']) ? $attributes['additional'] : '';
		
		$html = <<<EOD
			<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
			
			{$hidden}

			<label for="{$name}">{$label}</label>

			<div id="{$name}WYSIWYG" class="{$name}WYSIWYG">
			  <p>{$initialText}</p>
			</div>

			<script src="https://cdn.quilljs.com/1.0.0/quill.js"></script>
			<script type="text/javascript">
				var quill = new Quill('#{$name}WYSIWYG', {
				    modules: { toolbar: true },
				    theme: 'snow'
				});

				
				quill.on('text-change', function(delta) {
				  var text = $('#{$name}WYSIWYG .ql-editor').html();
				  $('.{$name}InputField').val(text);
				  console.log(text);
				});

			</script>
EOD;

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

		if(!empty($settings['label'])){
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
		return '</form '. Dotz::grabKey($attributes, 'data') .">\n";
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
