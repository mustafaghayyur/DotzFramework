<?php
namespace DotzFramework\Modules\Form;

use DotzFramework\Modules\Form\FormGenerator;

class Element {

	public $obj;

	public $callback;

	public function __construct($n, $funcName, $systemBoundValue = null){
		
		$this->obj = [ 
			'name' => $n,
			'systemBoundValue' => $systemBoundValue 
		];

		$this->callback = $funcName;
	}

	public function show(){
		echo FormGenerator::{$this->callback}((array)$this->obj);
	}

	public function get(){
		return FormGenerator::{$this->callback}((array)$this->obj);
	}

	public function method($m){
		$this->obj['method'] = $m;
		return $this;
	}

	public function action($a){
		$this->obj['action'] = $a;
		return $this;
	}

	public function value($v){
		$this->obj['value'] = $v;
		return $this;
	}

	public function label($l){
		$this->obj['label'] = $l;
		return $this;
	}

	public function type($t){
		$this->obj['type'] = $t;
		return $this;
	}

	public function options($o){
		$this->obj['options'] = $o;
		return $this;
	}

	public function option($key, $value){
		$this->obj['options'][$key] = $value;
		return $this;
	}

	public function default($k){
		$this->obj['default'] = $k;
		return $this;
	}

	public function data($d){
		$this->obj['data'] = $d;
		return $this;
	}

}