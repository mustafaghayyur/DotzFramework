<?php
namespace DotzFramework\Core;

use DotzFramework\Utilities\FileIO;

class Configurations{

	/**
	 * Holds the props for the framework.
	 * Though it can be accessed directy; it is recommended 
	 * to use Dotz::config() instead.
	 */
	public $props;
	
	/**
	 * The constructor handles the loading of all configs
	 * found in the configs/ directory.
	 */
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

	/**
	 * helper function used by the constructor
	 */
	public function loadFile($file){
		
		$c = new FileIO($file, 'r');
		$contents = $c->read();

		if($contents){

			return json_decode($contents);

		}else{

			return new \stdClass();
		
		}
	}

	/**
	 * Returns an array of 'package' => 'version' pairs.
	 *
	 * The version is made into an integer. Removing any
	 * letters, -, _, . and the space character.
	 *
	 * Useful for making decisons based on package/version
	 * installed.
	 *
	 * @return array
	 */
	public function getComposerPackages(){

		$f = new FileIO($this->props->app->systemPath . '/vendor/composer/installed.json', 'r');

		if(!$f->ok){ 
			return false; 
		}

		$packages = json_decode( $f->read() );

		$data = [];
		foreach ($packages as $package) {
		    $data[$package->name] = (int)preg_replace('#[\.\-\_a-zA-Z ]#', '', $package->version);
		}

		// incase you're running outside of composer...
		$data['dotz/framework'] = isset($data['dotz/framework']) 
			? $data['dotz/framework'] 
			: 100011;

		return $data;
	}

}