<?php

/*
 * This class is okay -- it sleeps all night, and it works all day.
 */
class Lumberjack {
	protected $_file;

	public function __construct($filename) {
		$dir = preg_replace('/\/[^\/]*$/S', '', $filename);
		if(!is_dir($dir)) {
			mkdir($dir, 0777, true);
		}

		$this->_file = fopen($filename, 'a');
	}

	public function log($msg) {
		$timestamp = date('r');
		fwrite($this->_file, '[' . $timestamp . '] ' . $msg . "\n");
	}

	public function __destruct() {
		fclose($this->_file);
	}
}