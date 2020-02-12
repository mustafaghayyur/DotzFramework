<?php
namespace DotzFramework\Utilities;

use DotzFramework\Core\Dotz;

/**
 * Should be instantiated with a $formDefinition injection,
 * before being sent to the view an FormBuilder instance object.
 */
class FormBuilder{
	
	/**
	 * Expected Format:
	 * $form = [];
	 * $form['definition'] = [ 'name' => '', 'action' => '', 'class' => '' ];
	 * $form['fields'] = [];
	 * $form['fields']['key1'] = [ 'name' => '', 'type' => '', 'value' => '', 'class' => '' ];
	 */
	public $formDefinition = array();

	public function __construct($formDefinition){

		if(!is_array($formDefinition)){
			if(is_object($formDefinition)){
				$def = (array)$formDefinition;
				unset($formDefinition);
				$formDefinition = $def;
			}
		}else{
			return null;
		}

		$this->formDefinition = $formDefinition;

	}

	/**
	 * Generates HTML for a specified $field.
	 *
	 * $field can be a string identifier for a predefined field in 
	 * $this->formDefinition['fields'] array. Or, it can be a new array to be 
	 * used instead of the data stored in $this->formDefinition['fields'].
	 */
	public function generateField($field){

		if(self::isValidArray($field)){
			
			$f = $field;
			if(isset($f['name'])){
				$this->formDefinition['fields'][$f['name']] = $field;
			}
		
		}else{
			
			if(is_string($field)){
				
				if(isset($this->formDefinition['fields'][$field])){
					$f = $this->formDefinition['fields'][$field];
				}else{
					return '<!-- Could not fetch field: '. $field .' -->';
				}

			}else{
					return '<!-- Could not fetch field: '. $field .' -->';
			}
		}

		$html = '<input ';
		
		foreach ($f as $key => $value) {
			$html .= ' '.$key.'="'. $value .'"';
		}

		$html .= ' />';

		return $html;
	}

	public function generateOpeningTag(){
		
		if(!isset($this->formDefinition['definition'])
				|| !is_array($this->formDefinition['definition'])){
			
				return '<!-- The "definition" key and its values are missing in your form definition object. Cannot call generateOpeningTag() -->';

		}

		$f = $this->formDefinition['definition'];

		$html = '<form ';
		
		foreach ($f as $key => $value) {
			$html .= ' '.$key.'="'. $value .'"';
		}

		$html .= '>';

		return $html;
	}

	public function generateClosingTag(){

		$data = (isset($this->formDefinition['definition']['closing data'])) ? $this->formDefinition['definition']['closing data'] : '';

		$html = '</form '. $data .'>';

		return $html;
	}

	/**
	 * Checks if a given variable is an array with keys that are all string vakues,
	 */
	public static function isValidArray($array){
		
		if(is_array($array)){
			$keys = array_keys($array);

			foreach ($keys as $key) {
				if(is_int($key)){
					return false;
				}
			}

			return true;
		}

		return false;
	}

}