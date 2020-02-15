<?php
namespace DotzFramework\Core;

class View{

	public $configs;
	public function __construct(){
		$dotz= Dotz::get();
		$this->configs = $dotz->container['configs']->props;
	}

	/**
	 * Use for HTML outputs.
	 * 
	 * Params:
	 *  - $view - file name (without the extension)
	 *  - $path - subdirectory (if any) where view resides
	 *  - $data - assoc. array of non-numeric keys that carry 
	 *  		  objects/arrays/data to be passed along to the view.
	 *
	 * For example, to send an array of names called $employees, 
	 * you would call the load method with the following parameters:
	 * 
	 * `  Views::load('employee_list', '', [ 'employees' => $employees ]);`
	 */
	public function load($view, $path = '', $data){
		if(!$this->viewsConfigsOk()){
			throw new \Exception('Views configurations not set correctly.');
		}

		if(!self::dataOk($data)){
			$data = [
				'error' => 'Data passed to View was not formatted correctly. All data should be keyed with a string identifier.',
				'data' => $data
			];
		}

		$path = trim($path, '/') .'/';
		$file = $this->configs->views->directory .'/'. $path . $view .'.php';

		if(!file_exists($file)){
			throw new \Exception('View not found in View::load().');			
		}

		include_once($file);
	}

	/**
	 * Use for jSON outputs.
	 */
	public function sendToJson($data){
		$o = json_encode($data);
		
		if($o){
			header('Content-Type: application/json');
			echo $o;
			die();
		}
	}

	protected function viewsConfigsOk(){
		if(isset($this->configs->views)
				&& is_object($this->configs->views)){

			if(isset($this->configs->views->directory)){
				if(file_exists($this->configs->views->directory)){
					return true;
				}
			}
		}

		return false;
	}

	protected static function dataOk($data){
		return \DotzFramework\Utilities\FormBuilder::isValidArray($data);
	}

}