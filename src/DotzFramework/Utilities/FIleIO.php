<?php
namespace DotzFramework\Utilities;


/**
 * Handles reading / writing from files with ease.
 * 
 * Please note: each fopen mode has its own limitations.
 *
 * We try to accomodate for some. 
 * 
 * However, incorrct use of certain operations in certain 
 * modes will still throw errors. 
 *
 * Please see: https://www.php.net/manual/en/function.fopen.php
 */
class FileIO {

	/**
	 * Holds the file name requested.
	 */
	private $file;

	/**
	 * Holds the file socket
	 */
	private $socket;

	/**
	 * open mode for socket
	 */
	private $mode;

	/**
	 * status of socket connection
	 */
	public $ok;

	public function __construct($file, $mode){
		
		$mode = self::formatMode($mode);

		$this->file = $file;
		$this->mode = $mode;
		$this->socket = fopen($file, $mode);
		$this->ok = false;

		if($this->socket){
			$this->ok = true;
		}
	}

	/**
	 * Read from the open file if $mode permits this.
	 */
	public function read($byteLength = null){
		
		if(is_int($byteLength)){
			return fread($this->socket, $byteLength);
		}

		$size = filesize($this->file);
		return fread($this->socket, $size);

	}

	/**
	 * Write into current file if your open
	 * $mode permits this.
	 */
	public function write($text, $length = null){
		
		if($this->mode === 'r'){
			return false;
		}

		if(is_int($length)){
			$r = fwrite($this->socket, $text, $length);
		}else{
			$r = fwrite($this->socket, $text);
		}

		if($r === false){
			throw new \Exception('Could not write to file: '.$this->file.' | ['.substr($text, 0, 15).'...]');
		}

		return $this;
	}

	/**
	 * Seek a certain position in file. Then chain next
	 * read/write command to the end for smooth file
	 * operations. 
	 * 
	 * If your mode/permissions allow this.
	 *
	 * Tries to guess your $flag intelligently.
	 */
	public function seek($offsetBytes, $flag = SEEK_SET){
		
		$flag = self::guessSeekFlag($flag);
		
		if(fseek($this->socket, $offsetBytes, $flag) === 0){
			return $this;
		}

		return false;
	}

	/**
	 * The fopen() mode can be a point of irritation
	 * when you cannot remember if the mode should be:
	 * 	- 'r+' or '+r'
	 *
	 * We try to remedy that delimma for you.
	 */
	protected static function formatMode($mode){
		
		if(strpos($mode, '+') === 0){
			$m = substr($mode, 1);
			return $m.'+';
		}

		return $mode;
	}

	/**
	 * Flags can be hard to remember.
	 * We try to guess out your option.
	 */
	public static function guessSeekFlag($flag){
		
		if(in_array($flag, ['SEEK_SET', 'SEEK_CUR', 'SEEK_END'])){
			return $flag;
		}

		if(stripos($flag, 'begin') !== false){
			return SEEK_SET;
		}

		if(stripos($flag, 'default') !== false){
			return SEEK_SET;
		}

		if(stripos($flag, 'cur') !== false){
			return SEEK_CUR;
		}

		if(stripos($flag, 'end') !== false){
			return SEEK_END;
		}

		return SEEK_SET;

	}

	public function __destruct(){
		if($this->ok) fclose($this->socket);
	}

	/**
	 * ============================================================
	 * 				BELOW ARE DEPRECATED FUNCTIONS
	 * ============================================================
	 */

	public function readEntireFile(){
		return $this->read(null);
	}

	public function readUpTo($byteLength = 25){
		return $this->read($byteLength);
	}

	public function writeString($text, $length = null){
		return $this->write($text, $length);
	}

	public function setFilePointer($offsetBytes, $flag){
		return $this->seek($offsetBytes, $flag);
	}
}