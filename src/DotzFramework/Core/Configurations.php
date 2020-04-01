<?php
namespace DotzFramework\Core;

use DotzFramework\Utilities\FileIO;

class Configurations{

	public $props;
	
	public function __construct($path){

		$this->props = new \stdClass();

		$files = scandir($path);

		if(is_array($files)){

			foreach ($files as $file) {
				$fParts = explode('.', $file);
				
				// This is a file. Add it to the files bucket and extract.
				if(isset($fParts[1]) && !empty($fParts[0]) && !empty($fParts[1])){
					
					$this->props->{$fParts[0]} = $this->loadFile($path .'/'. $file);
					
					if(json_last_error() !== JSON_ERROR_NONE){
						throw new \Exception("Configuration file {$fParts[0]}.txt could not be validated due to a JSON formatting error.");
					}
				}
				
			}
		}

	}

	public function loadFile($file){
		
		$c = new FileIO($file, 'r');
		$contents = $c->readEntireFile();

		if($contents){

			return json_decode($contents);

		}else{

			return new \stdClass();
		
		}
	}

}