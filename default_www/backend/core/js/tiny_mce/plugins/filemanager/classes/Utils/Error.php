<?php
/**
 * $Id: Error.php 624 2008-12-02 17:44:39Z spocke $
 *
 * @package MCManager.utils
 * @author Moxiecode
 * @copyright Copyright © 2005, Moxiecode Systems AB, All rights reserved.
 */

// Define it on PHP4
if (!defined('E_STRICT'))
	define('E_STRICT', 2048);

// Define error levels
define('FATAL', E_USER_ERROR);
define('ERROR', E_USER_WARNING);
define('WARNING', E_USER_NOTICE);

/**
 * This class handles Error messages.
 *
 * @package MCManager.utils
 */
class Moxiecode_Error {
	var $id;

	function Moxiecode_Error() {
	}

	function setId($id) {
		$this->id = $id;
	}

	function handleError($errno, $errstr, $errfile, $errline, $errcontext) {
		global $man;

		$error = array();
		$log = false;

		$error['title'] = "";
		$error['break'] = false;
		$error['errstr'] = $errstr;
		//$error['errcontext'] = $errcontext;
		$error['errcontext'] = "";
		$error['errfile'] = "";
		$error['errline'] = "";

		// Add file and line only in debug mode
		if (isset($man)) {
			$mcConfig = $man->getConfig();
			$log = $man->getLogger();

			if (checkBool($mcConfig['general.debug'])) {
				$error['errfile'] = $errfile;
				$error['errline'] = $errline;
			}
		}

		switch ($errno) {
			case E_USER_ERROR:
				$error['title'] = "Fatal Error";
				$error['break'] = true;
			break;

			case E_USER_NOTICE:
				$error['title'] = "Notice";
				$error['break'] = false;
			break;

			case E_USER_WARNING:
				$error['title'] = "Warning";
				$error['break'] = true;
			break;

			case E_PARSE:
				$error['title'] = "PHP Parse Error";
				$error['break'] = true;

				if ($log)
					$log->fatal($error['title'] . ", Msg: " . $error['errstr'] . " in " . $error['errfile'] . "(" . $error['errline'] . ")");
			break;

			case E_ERROR:
				$error['title'] = "PHP Error";
				$error['break'] = true;

				if ($log)
					$log->error($error['title'] . ", Msg: " . $error['errstr'] . " in " . $error['errfile'] . "(" . $error['errline'] . ")");
			break;

			case E_WARNING:
				$error['title'] = "PHP Warning";
				$error['break'] = false;

				if ($log)
					$log->warn($error['title'] . ", Msg: " . $error['errstr'] . " in " . $error['errfile'] . "(" . $error['errline'] . ")");
			break;

			case E_CORE_ERROR:
				$error['title'] = "PHP Error : Core Error";
				$error['break'] = true;

				if ($log)
					$log->error($error['title'] . ", Msg: " . $error['errstr'] . " in " . $error['errfile'] . "(" . $error['errline'] . ")");
			break;

			case E_CORE_WARNING:
				$error['title'] = "PHP Error : Core Warning";
				$error['break'] = true;

				if ($log)
					$log->warn($error['title'] . ", Msg: " . $error['errstr'] . " in " . $error['errfile'] . "(" . $error['errline'] . ")");
			break;

			case E_COMPILE_ERROR:
				$error['title'] = "PHP Error : Compile Error";
				$error['break'] = true;

				if ($log)
					$log->error($error['title'] . ", Msg: " . $error['errstr'] . " in " . $error['errfile'] . "(" . $error['errline'] . ")");
			break;

			case E_COMPILE_WARNING:
				$error['title'] = "PHP Error : Compile Warning";
				$error['break'] = true;

				if ($log)
					$log->warn($error['title'] . ", Msg: " . $error['errstr'] . " in " . $error['errfile'] . "(" . $error['errline'] . ")");
			break;

			case E_NOTICE:
				$error['title'] = "PHP Notice";
				$error['break'] = false;

				if ($log)
					$log->info($error['title'] . ", Msg: " . $error['errstr'] . " in " . $error['errfile'] . "(" . $error['errline'] . ")");
			break;

			case E_STRICT:
				$error['title'] = "PHP Strict";
				$error['break'] = false;

				if ($log)
					$log->info($error['title'] . " (" . $errno . ")" . ", Msg: " . $error['errstr'] . " in " . $error['errfile'] . "(" . $error['errline'] . ")");
			break;
		}

		// Add error number
		$error['title'] = $error['title'] . " (". $errno .")";

		return $error;
	}
}

/**
 * Calls the MCError class, returns true.
 *
 * @param Int $errno Number of the error.
 * @param String $errstr Error message.
 * @param String $errfile The file the error occured in.
 * @param String $errline The line in the file where error occured.
 * @param Array $errcontext Error context array, contains all variables.
 * @return Bool Just return true for now.
 */
function JSONErrorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
	global $MCErrorHandler;

	// Ignore these
	if ($errno == E_STRICT)
		return true;

	// Just pass it through	to the class.
	$data = $MCErrorHandler->handleError($errno, $errstr, $errfile, $errline, $errcontext);

	if ($data['break']) {
		unset($data['break']);
		unset($data['title']);

		$data['level'] = "FATAL";

		$json = new Moxiecode_JSON();
		$result = new stdClass();
		$result->result = null;
		$result->id = 'err';
		$result->error = $data;

		echo $json->encode($result);
		die();
	}
}

/**
 * Calls the MCError class, returns true.
 *
 * @param Int $errno Number of the error.
 * @param String $errstr Error message.
 * @param String $errfile The file the error occured in.
 * @param String $errline The line in the file where error occured.
 * @param Array $errcontext Error context array, contains all variables.
 * @return Bool Just return true for now.
 */
function JSErrorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
	global $MCErrorHandler;

	// Ignore these
	if ($errno == E_STRICT)
		return true;

	// Just pass it through	to the class.
	$data = $MCErrorHandler->handleError($errno, $errstr, $errfile, $errline, $errcontext);

	if ($data['break']) {
		echo 'alert(\'' . addslashes($data['errstr']) . '\');';
		die();
	}
}

/**
 * Calls the MCError class, returns true.
 *
 * @param Int $errno Number of the error.
 * @param String $errstr Error message.
 * @param String $errfile The file the error occured in.
 * @param String $errline The line in the file where error occured.
 * @param Array $errcontext Error context array, contains all variables.
 * @return Bool Just return true for now.
 */
function StreamErrorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
	global $MCErrorHandler;

	// Ignore these
	if ($errno == E_STRICT)
		return true;

	// Just pass it through	to the class.
	$data = $MCErrorHandler->handleError($errno, $errstr, $errfile, $errline, $errcontext);

	if ($data['break']) {
		if ($_SERVER["REQUEST_METHOD"] == "GET") {
			header("HTTP/1.1 500 Internal server error");
			die($errstr);
		} else {
			unset($data['break']);
			unset($data['title']);

			$data['level'] = "FATAL";

			$json = new Moxiecode_JSON();
			$result = new stdClass();
			$result->result = null;
			$result->id = 'err';
			$result->error = $data;

			echo '<html><body><script type="text/javascript">parent.handleJSON(' . $json->encode($result) . ');</script></body></html>';
			die();
		}
	}
}

/**
 * Calls the MCError class, returns true.
 *
 * @param Int $errno Number of the error.
 * @param String $errstr Error message.
 * @param String $errfile The file the error occured in.
 * @param String $errline The line in the file where error occured.
 * @param Array $errcontext Error context array, contains all variables.
 * @return Bool Just return true for now.
 */
function HTMLErrorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
	global $MCErrorHandler;

	// Ignore these
	if ($errno == E_STRICT)
		return true;

	// Just pass it through	to the class.
	$data = $MCErrorHandler->handleError($errno, $errstr, $errfile, $errline, $errcontext);

	if ($data['break']) {
		die($errstr);
	}
}


?>