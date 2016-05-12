<?php

class B_Exception extends Exception {
	public $description;
	public $level;
	public $file;
	public $line;
	public $code;
	public $message;

	public function __construct($level, $string, $file, $line) {
		$this->level = $level;
		$this->code = "WW2PHP_RUNTIME_ERROR";
		$this->file = $file;
		$this->line = $line;
		Exception::__construct($string);
	}
}