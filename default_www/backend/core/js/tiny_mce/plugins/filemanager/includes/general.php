<?php
/**
 * general.php
 *
 * @package MCManager.includes
 * @author Moxiecode
 * @copyright Copyright © 2007, Moxiecode Systems AB, All rights reserved.
 */

@error_reporting(E_ERROR | E_WARNING | E_PARSE);

if (function_exists('date_default_timezone_set'))
	date_default_timezone_set('GMT');

if (function_exists('set_magic_quotes_runtime'))
	@set_magic_quotes_runtime(false);

// Load logger class
require_once(dirname(__FILE__) . "/../classes/Utils/Logger.class.php");
$httpRequestInput = array_merge($_GET, $_POST);

/**
 * Returns an request value by name without magic quoting.
 *
 * @param String $name Name of parameter to get.
 * @param String $default_value Default value to return if value not found.
 * @return String request value by name without magic quoting or default value.
 */
function getRequestParam($name, $default_value = false, $sanitize = false) {
	global $httpRequestInput;

	if (!isset($httpRequestInput[$name]))
		return $default_value;

	if (is_array($httpRequestInput[$name])) {
		$newarray = array();

		foreach ($httpRequestInput[$name] as $name => $value)
			$newarray[formatParam($name, $sanitize)] = formatParam($value, $sanitize);

		return $newarray;
	}

	return formatParam($httpRequestInput[$name], $sanitize);
}

function formatParam($str, $sanitize = false) {
	if ($sanitize)
		$str = preg_replace("/[^0-9a-z\-_,]+/i", "", $str);

	if (ini_get("magic_quotes_gpc"))
		$str = stripslashes($str);

	return $str;
}

function getClassName($obj) {
	return strtolower(get_class($obj));
}

/**
 * Check if a value is true/false.
 *
 * @param string $str True/False value.
 * @return bool true/false
 */
function checkBool($str, $def = false) {
	if ($str === true)
		return true;

	if ($str === false)
		return false;

	$str = strtolower($str);

	if ($str == "true")
		return true;

	return $def;
}

/**
 * Returns a file extention from a path.
 *
 * @param string $path Path to grab extention from.
 * @return string File extention.
 */
function getFileExt($path) {
	$ar = explode('.', $path);
	return strtolower(array_pop($ar));
}

/**
 * Returns the mime type of an path by resolving it agains a apache style "mime.types" file.
 *
 * @param String $path path to Map/get content type by
 * @patam String $mime_File Absolute filepath to mime.types style file.
 * @return String mime type of path or an empty string on failue.  
 */
function mapMimeTypeFromUrl($path, $mime_file) {
	if (($fp = fopen($mime_file, "r"))) {
		$ar = explode('.', $path);
		$ext = strtolower(array_pop($ar));

		while (!feof ($fp)) {
			$line = fgets($fp, 4096);
			$chunks = preg_split("/(\t+)|( +)/", $line);

			for ($i=1; $i<count($chunks); $i++) {
				if (rtrim($chunks[$i]) == $ext)
					return $chunks[0];
			}
		}

		fclose($fp);
	}

	return "";
}

/**
 * Adds no cache headers to HTTP response.
 */
function addNoCacheHeaders() {
	// Date in the past
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

	// always modified
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

	// HTTP/1.1
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);

	// HTTP/1.0
	header("Pragma: no-cache");
}

/**
 * Returns var_dump.
 *
 * @return string A var_dump returned as string.
 */
function varDump($data) {
	ob_start();
	var_dump($data);
	$contextcontent = ob_get_contents();
	ob_end_clean();

	return $contextcontent;
}

/**
 * A proper IndexOf function, cause strpos suxx0rz!
 *
 * @return int Returns -1 if failed, else position.
 */
function indexOf($str, $search, $offset=0) {
	$pos = strpos($str, $search, $offset);
	
	if ($pos === false)
		return -1;

	return $pos;
}

function &getLogger() {
	global $mcLogger, $man;

	if (isset($man))
		$mcLogger = $man->getLogger();

	if (!$mcLogger) {
		$mcLogger = new Moxiecode_Logger();

		// Set logger options
		$mcLogger->setPath(dirname(__FILE__) . "/../logs");
		$mcLogger->setMaxSize("100kb");
		$mcLogger->setMaxFiles("10");
		$mcLogger->setFormat("{time} - {message}");
	}

	return $mcLogger;
}

function debug($msg) {
	$args = func_get_args();

	$log =& getLogger();
	$log->debug(implode(', ', $args));
}

function info($msg) {
	$args = func_get_args();

	$log =& getLogger();
	$log->info(implode(', ', $args));
}

function error($msg) {
	$args = func_get_args();

	$log =& getLogger();
	$log->error(implode(', ', $args));
}

function warn($msg) {
	$args = func_get_args();

	$log =& getLogger();
	$log->warn(implode(', ', $args));
}

function fatal($msg) {
	$args = func_get_args();

	$log =& getLogger();
	$log->fatal(implode(', ', $args));
}

?>