<?php
namespace DotzFramework\Utilities;

/**
 * Helps in adding JS you wish to send to output.
 */
class JSOutput {

	protected $output;

	function __construct(){
		$this->output = [];
	}

	function add($key, $string){
		$this->output[$key] = $string;
	}

	function remove($key){
		unset($this->output[$key]);
	}

	function stringify(){
		$o = "\n<script type=\"text/javascript\">\n";
		$o .= implode("\n 	", $this->output);
		$o .= "\n</script>\n";

		return $o;
	}
}