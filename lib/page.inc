<?php
class CPage {
	private $_buffer;

	public function __construct () {
		$this->_buffer = "";
	}

	public function __destruct () {
		unset ($this->_buffer);
	}

	public function dump () {
		echo $this->_buffer;
	}

	public function require ($page) {
		ob_start ();
		require_once $page;
		$this->_buffer = ob_get_contents ();
		ob_end_clean ();
	}

	public function add($code) {
		$this->_buffer .= $code;
	}
}
?>