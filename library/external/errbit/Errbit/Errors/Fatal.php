<?php

/**
 * Errbit PHP Notifier.
 *
 * Copyright © Flippa.com Pty. Ltd.
 * See the LICENSE file for details.
 */

class Errbit_Errors_Fatal extends Errbit_Errors_Base {
	/**
	 * Create a new fatal error wrapping the given error context info.
	 */
	public function __construct($message, $line, $file) {
		parent::__construct(
			$message,
			$line,
			$file,
			array(
				array(
					'line'     => $line,
					'file'     => $file,
					'function' => '<unknown>'
				)
			)
		);
	}
}
