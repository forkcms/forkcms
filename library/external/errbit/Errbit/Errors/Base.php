<?php

/**
 * Errbit PHP Notifier.
 *
 * Copyright © Flippa.com Pty. Ltd.
 * See the LICENSE file for details.
 */

/**
 * Converts a native PHP error, notice, or warning into something that
 * sort of resembles an Exception.
 *
 * If PHP's Exception class wasn't so f***ing stupid and didn't make
 * everything final, this would inherit from it, but alas...
 */
class Errbit_Errors_Base {
	private $_message;
	private $_line;
	private $_file;
	private $_trace;

	/**
	 * Create a new error wrapping the given error context info.
	 */
	public function __construct($message, $line, $file, $trace) {
		$this->_message = $message;
		$this->_line    = $line;
		$this->_file    = $file;
		$this->_trace   = $trace;
	}

	public function getMessage() {
		return $this->_message;
	}

	public function getLine() {
		return $this->_line;
	}

	public function getFile() {
		return $this->_file;
	}

	public function getTrace() {
		return $this->_trace;
	}
}
