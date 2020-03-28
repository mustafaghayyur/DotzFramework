<?php
namespace DotzFramework\Utilities;

class FIleIO {
	
	private $fileName;
	private $fp;
	public $ok;

	function __construct($file, $mode){
		$this->fileName = $file;
		$this->ok = false;
		
		$this->fp = @fopen($file, $mode);

		if($this->fp){
			$this->ok = true;
		}
	}

	function readEntireFile(){
		$size = filesize($this->fileName);
		return fread($this->fp, $size);
	}

	function readUpTo($byteLength = 25){
		return fread($this->fp, $byteLength);
	}

	function writeString($text, $length = null){
		return fwrite($this->fp, $text, $length);
	}

	/**
	 * $flag can be [SEEK_SET | SEEK_CUR | SEEK_END]
	 */
	function setFilePointer($offsetBytes, $flag){
		return fseek($this->fp, $offsetBytes, $flag);
	}

	function __destruct(){
		if($this->ok) fclose($this->fp);
	}
}