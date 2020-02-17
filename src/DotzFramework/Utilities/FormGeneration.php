<?php
namespace DotzFramework\Utilities;

use DotzFramework\Core\Dotz;

class FormGeneration{
	
	public function open($attributes){
		$html = '<!-- Could not generate form opening tag -->';

		if(is_array($attributes) && isset($attributes['attr'])){
			$html = '<form';
			$html .= self::generateAttributesString($attributes['attr']);
			$html .= ">\n";	
		}
		
		return $html;
	}

	/**
	 * Generates HTML for a specified $field definition.
	 */
	public function input($field, $generateLabel = true){

		$html = '<!-- Could not fetch field: '. $field .' -->';

		if(is_array($field)){
			$html = '';

			if($generateLabel && !empty($field['label'])){
				$html = '<label for="'.$field['attr']['name'].'" >'.$field['label'].': </label>';
			}

			$html .= '<input';
			$html .= self::generateAttributesString($field['attr']);
			$html .= " />\n";
		}

		return $html;
	}

	public function textarea($field, $generateLabel = true){

		$html = '<!-- Could not fetch field: '. $field .' -->';

		if(is_array($field)){
			$html = '';

			if($generateLabel && !empty($field['label'])){
				$html = '<label for="'.$field['attr']['name'].'" >'.$field['label'].': </label>';
			}

			$html .= '<textarea';
			$html .= self::generateAttributesString($field['attr']);
			$html .= '>';
			$html .= isset($field['text']) ? $field['text'] : '';
			$html .= "</textarea>\n";
		}

		return $html;
	}


	public function select($field, $generateLabel = true){

		$html = '<!-- Could not fetch field: '. $field .' -->';

		if(isset($field['attr']) && is_array($field['attr'])){
			if(isset($field['options']) && is_array($field['options'])){
				$html = '';
				$label = '';

				if($generateLabel && !empty($field['label'])){
					$label = '<label for="'.$field['attr']['name'].'" >'.$field['label'].': </label>';
				}

				$openingTag = '<select';
				$openingTag .= self::generateAttributesString($field['attr']);
				$openingTag .= '>';

				$options = '';
				foreach ($field['options'] as $key => $optionArray) {
					
					$option = '<option';
					$option .= self::generateAttributesString($optionArray['attr']);
					$option .= '>';
					$option .= $optionArray['displayText'];
					$option .= "</option>\n";

					$options .= $option;
				}

				$closingTag = '</select>';

				$html = $label ."\n". $openingTag ."\n". $options . $closingTag ."\n";
			}
		}

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