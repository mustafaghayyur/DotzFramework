<?php
namespace DotzFramework\Core;

class ErrorHandler {

	public function handle($severity, $message, $file, $line) {
	    //var_dump('hello');die();
	    if (!(error_reporting() & $severity)) {
	        return; // This error code is not included in error_reporting
	    }

	    throw new \ErrorException($message, 0, $severity, $file, $line);
	}

	public function output($e){

		$output = [
			'status' => 'error', 
			'msg' => $e->getMessage(), 
			'file' => $e->getFile(), 
			'line' => $e->getLine()
		];

		if(strpos($output['msg'], 'Undefined property:') !== false){
			
			if(strpos($output['msg'], 'MySQLQuery::$connection') !== false){
				$output['updateError'] = 'We have changed MySQLQuery::$connection to MySQLQuery::$pdo in v0.2.2 of Dotz. Please update your application code accordingly.';
			}

		}

		if(strpos($output['msg'], '[Update Error] - ') !== false){
			
			$output['updateError'] = substr($output['msg'], 16);
			unset($output['msg']);
			unset($output['file']);
			unset($output['line']);
		
		}

		if(strpos(Router::$controllerUsed, 'Resource') === false){
			Dotz::get()->load('view')->load('error', $output);
		}else{
			Dotz::get()->load('view')->json($output);
		}
	}

}