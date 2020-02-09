<?php
namespace Core;

use Utilities\FileIO;

class Configurations(){
	
	protected function __construct($path){

		$files = scandir($path);

		if(is_array($files)){
			$this->files = [];

			foreach ($files as $file) {
				$this->files[] = $path .'/'. $file;
				$this->values = $this->loadFile($path .'/'. $file);
			}
		}




	}

	public function loadFile($file){
		
		$c = new FileIO($file, 'r');
		$contents = $c->readEntireFile();

		if($contents){

			return json_decode($contents);

		}else{

			return false;
		
		}
	}

}