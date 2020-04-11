<?php
namespace DotzFramework\Core;

use Exception;

class ErrorHandler {

	/**
	 * Stores an array of PHP exceptions that will
	 * not terminate the script, but should be 
	 * displayed at the end of execution.
	 */
	public $notices;

	/**
	 * has ErrorHandler::notices() been called
	 * yet? Since the method is called twice,
	 * this property ensures the method is executed
	 * only once.
	 */
	public $called = false;

	public const ERROR1 = "[Update Error] In version 0.2.3 of Dotz Framework; we have changed the properties in file 'configs/app.txt' relating to CSRF checks. Please closely inspect and mimic the changes found in 'vendor/dotz/framework/configs/app.txt'. In particular, you need to copy over the 'csrf' object property (with all its children properties) into your app.txt config file to proceed. You may change the values of these children properties as needed by your project.";

	/**
	 * Sets the $this->notices initial state.
	 */
	public function __construct(){
		$this->notices = [];
	}

	/**
	 * PHP error handler function definition.
	 * Used by set_error_handler();
	 */
	public function handle($severity, $message, $file, $line) {

	    if (!(error_reporting() & $severity)) {
	        return; // This error code is not included in error_reporting
	    }

	    if( in_array($severity, [E_USER_ERROR, E_RECOVERABLE_ERROR]) ) {
        	throw new \ErrorException($message, 0, $severity, $file, $line);
		}else{
			$this->notices[] = ['message' => $message, 'file'=> $file, 'line' => $line];
		}
	}

	/**
	 * Exceptions that terminate the script are handled
	 * by this output function.
	 */
	public function output($e){

		$output = [
			'status' => 'error', 
			'msg' => $e->getMessage(), 
			'file' => $e->getFile(), 
			'line' => $e->getLine(),
			'trace' => self::trace($e->getTrace()),
		];

		$output['updateError'] = self::updateMsg($output['msg'].$output['file']);

		if(strpos($output['msg'], '[Update Error] - ') !== false){
			
			$output['updateError'] = substr($output['msg'], 16);
			unset($output['msg']);
			unset($output['file']);
			unset($output['line']);
		
		}

		if(empty($output['updateError'])){
			unset($output['updateError']);
		}

		if(strpos(Dotz::module('router')->controllerUsed, 'Resource') === false){
			Dotz::module('view')->load('error', $output);
		}else{
			$output['trace'] = $e->getTrace();
			$output['message'] = Dotz::grabKey($output,'msg');
			unset($output['msg']);
			Dotz::module('view')->json($output);
		}
	}

	/**
	 * Formulates the Exception trace.
	 * Note that the trace can often be inaccurate.
	 */
	public static function trace($stack){

		$html = '';

		foreach ($stack as $i => $v) {
			$html .= '['.$i.'] - [File: '.Dotz::grabKey($v,'file').'] - [Line: '.Dotz::grabKey($v,'line').']';
			$html .= ' - [Function: '.Dotz::grabKey($v,'function').'()]<br/><br/>';
		}

		return $html;
	}

	/**
	 * Static method to add an 'updateError' message where relevent.
	 * Useful for when Dotz changes around some definitions.
	 *
	 * We pass in the Exception message & file path as one string '$msg';
	 * as that string is only meant to be parsed for decision making.
	 * The actual Update-Message (if any) would be its own string.
	 */
	public static function updateMsg($msg){
		if(strpos($msg, 'Undefined property:') !== false){
			
			if(strpos($msg, 'MySQLQuery::$connection') !== false){
				return 'We have changed MySQLQuery::$connection to MySQLQuery::$pdo in v0.2.2 of Dotz. Please update your application code accordingly.';
			}

			if(strpos($msg, 'leIO::$fileName') !== false){
				return 'We have changed FIleIO::$fileName to FileIO::$file in v0.2.2 of Dotz. Please update your application code accordingly.';
			}

			if(strpos($msg, 'leIO::$fp') !== false){
				return 'We have changed FIleIO::$fp to FileIO::$socket in v0.2.2 of Dotz. Please update your application code accordingly.';
			}

		}
	}

	/**
	 * Gathers together all PHP notices/warnings/errors
	 * that have not terminated the script, and shows them 
	 * near the end.
	 */
	public function notices(){
		
		if($this->called){
			return true;
		}

		$this->called = true;

		if(count($this->notices) == 0){
			return;
		}

		if(Dotz::module('view')->jsonCalled){
			return;
		}

		$html = '<div class="notices">';
		$html .= '<h2>Notices:</h2>';

		foreach ($this->notices as $i => $v) {
			
			$u = self::updateMsg($v['message'].$v['file']);
			$u = empty($u) ? '' : ' - <strong>Update Error:</strong> '.$u.'<br/>';

			$html .= '<hr/><p><strong>Notice:</strong> <br/>'.
						$u.
						' - File: '.$v['file'].'<br/>'.
						' - <strong>Message:</strong> '.$v['message'].'<br/>'.
						' - Line: '.$v['line'].'<br/><br/></p>';
		}

		$html .="</div>";

		if(Dotz::module('view')->loadCalled){
			echo $html;
		}else{
			Dotz::module('view')->load('error', ['html' => $html, 'data' => $html]);
		}

	}

	/**
	 * Incase the index.php file doesn't call ErrorHandler::notices().
	 */
	public function __destruct(){
		$this->notices();
	}

}
