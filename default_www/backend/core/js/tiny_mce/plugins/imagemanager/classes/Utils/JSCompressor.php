<?php
/**
 * Moxiecode JS Compressor.
 *
 * @version 1.0
 * @author Moxiecode
 * @site http://www.moxieforge.com/
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 * @licence LGPL
 * @ignore
 */

/**
 * This class is used to compress JS files by minifying and gzipping them to reduce the overall download time for the JS of a site or system.
 *
 * @package Moxiecode_Utils
 */
class Moxiecode_JSCompressor {
	/**#@+ @access private */

	var $_items, $_settings, $_lastUpdate;

	/**#@-*/

	/**
	 * Constructs a new JS compressor instance.
	 *
	 * @param Array $settings Name/value array with settings for the compressor instance.
	 */
	function Moxiecode_JSCompressor($settings = array()) {
		$this->_items = array();

		$default = array(
			'expires_offset' => '10d',
			'disk_cache' => true,
			'cache_dir' => '_cache',
			'gzip_compress' => true,
			'remove_whitespace' => true,
			'charset' => 'UTF-8',
			'patch_ie' => true,
			'remove_firebug' => false,
			'name' => ''
		);

		$this->_settings = array_merge($default, $settings);
		$this->_lastUpdate = 0;
	}

	/**
	 * Add raw contents as part of the concatenation/compression. This method should only be used if
	 * you really need to.
	 *
	 * @param String $content Content to add as part of the concatenation process.
	 */
	function addContent($content) {
		$this->_items[] = array('content', $content);
	}

	/**
	 * Adds a file to the concatenation/compression process.
	 *
	 * @param String $path Path to the file to include in the compressed package/output.
	 * @param bool $whitespace Set this state to false to skip whitespace removal for the specified file.
	 */
	function addFile($path, $whitespace = true) {
		$this->_items[] = array('file', $path, $whitespace);

		$mtime = @filemtime($path);

		if ($mtime > $this->_lastUpdate)
			$this->_lastUpdate = $mtime;
	}

	/**
	 * Compress and output all files that got added to the process by addFile.
	 */
	function compress() {
		$key = "";

		foreach ($this->_items as $item)
			$key .= $item[1];

		// Setup some variables
		$cacheFile = $this->_settings['cache_dir'] . "/";

		if ($this->_settings['name'])
			$cacheFile .= preg_replace('/[^a-z0-9_]/i', '', $this->_settings['name']);
		else
			$cacheFile .= md5($key);

		$supportsGzip = false;
		$content = "";
		$encodings = array();

		// Check if it supports gzip
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
			$encodings = explode(',', strtolower(preg_replace("/\s+/", "", $_SERVER['HTTP_ACCEPT_ENCODING'])));

		if ($this->_settings['gzip_compress'] && (in_array('gzip', $encodings) || in_array('x-gzip', $encodings) || isset($_SERVER['---------------'])) && function_exists('gzencode') && !ini_get('zlib.output_compression')) {
			$enc = in_array('x-gzip', $encodings) ? "x-gzip" : "gzip";
			$supportsGzip = true;
			$cacheFile .= ".gz";
		} else
			$cacheFile .= ".js";

		// Set headers
		header("Content-type: text/javascript;charset=" . $this->_settings['charset']);
		header("Vary: Accept-Encoding");  // Handle proxies
		header("Expires: " . gmdate("D, d M Y H:i:s", time() + $this->_parseTime($this->_settings['expires_offset'])) . " GMT");
		header("Cache-Control: public, max-age=" . $this->_parseTime($this->_settings['expires_offset']));

		// Output explorer workaround or compressed file
		if (!isset($_GET["gz"]) && $supportsGzip && $this->_settings['patch_ie'] && strpos($_SERVER["HTTP_USER_AGENT"], "MSIE") !== false) {
			// Build request URL
			$url = $_SERVER["PHP_SELF"];

			if (isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"])
				$url .= "?" . $_SERVER["QUERY_STRING"] . "&gz=1";
			else
				$url .= "?gz=1";

			// This script will ensure that the gzipped script gets loaded on IE versions with the Gzip request chunk bug

			echo 'var gz;try {gz = new XMLHttpRequest();} catch(gz) { try {gz = new ActiveXObject("Microsoft.XMLHTTP");}';
			echo 'catch (gz) {gz = new ActiveXObject("Msxml2.XMLHTTP");}}';
			echo 'gz.open("GET", "' . $url . '", false);gz.send(null);eval(gz.responseText);';
			die();
		}

		// Use cached file
		if ($this->_settings['disk_cache'] && file_exists($cacheFile) && @filemtime($cacheFile) == $this->_lastUpdate) {
			if ($supportsGzip)
				header("Content-Encoding: " . $enc);

			echo $this->_getFileContents($cacheFile);
			return;
		}

		// Load content
		foreach ($this->_items as $item) {
			if ($item[0] == 'file')
				$chunk = $this->_getFileContents($item[1]);
			else
				$chunk = $item[1];

			// Remove UTF-8 BOM
			if (substr($chunk, 0, 3) == pack("CCC", 0xef, 0xbb, 0xbf))
				$chunk = substr($chunk, 3);

			if ($this->_settings['remove_whitespace'] && $item[2])
				$chunk = $this->_removeWhiteSpace($chunk);

			if (!$item[2])
				$chunk = "\n" . $chunk . ";\n";

			$content .= $chunk;
		}

		// Remove firebug calls
		if ($this->_settings['remove_firebug'])
			$content = preg_replace('/console\\.[^;]+;/', '', $content);

		// GZip content
		if ($supportsGzip) {
			header("Content-Encoding: " . $enc);
			$content = gzencode($content, 9, FORCE_GZIP);
		}

		// Write cache file
		if ($this->_settings['disk_cache']) {
			if (!is_dir($this->_settings['cache_dir']))
				@mkdir($this->_settings['cache_dir']);

			$this->_putFileContents($cacheFile, $content);

			if (@file_exists($cacheFile))
				@touch($cacheFile, $this->_lastUpdate);
		}

		// Output content to client
		echo $content;
	}

	/**#@+ @access private */

	function _removeWhiteSpace($content) {
		$this->_strings = array();
		$this->_count = 0;

		// Replace strings and regexps
		$content = preg_replace_callback('/\\\\(\"|\'|\\/)/', array(&$this, '_encode'), $content); // Replace all \/, \", \' with tokens
		$content = preg_replace_callback('/(\'[^\'\\n\\r]*\')|("[^"\\n\\r]*")|(\\s+(\\/[^\\/\\n\\r\\*][^\\/\\n\\r]*\\/g?i?))|([^\\w\\x24\\/\'"*)\\?:]\\/[^\\/\\n\\r\\*][^\\/\\n\\r]*\\/g?i?)/', array(&$this, '_strToItems'), $content);

		// Remove comments
		$content = preg_replace('/(\\/\\/[^\\n\\r]*[\\n\\r])|(\\/\\*[^*]*\\*+([^\\/][^*]*\\*+)*\\/)/', '', $content);

		// Remove whitespace
		$content = preg_replace('/[\r\n]+/', ' ', $content);
		$content = preg_replace('/\s*([=&|!+\\-\\/?:;,\\^\\(\\)\\{\\}<>%]+)\s*/', '$1', $content);
		$content = preg_replace('/(;)\s+/', '$1', $content);
		$content = preg_replace('/\s+/', ' ', $content);

		// Restore strings and regexps
		$content = preg_replace_callback('/¤@([^¤]+)¤/', array(&$this, '_itemsToStr'), $content);
		$content = preg_replace_callback('/¤#([^¤]+)¤/', array(&$this, '_decode'), $content); // Restore all \/, \", \'

		return $content;
	}

	function _putFileContents($path, $content) {
		if (!is_writable($path))
			return;

		if (function_exists("file_put_contents"))
			return @file_put_contents($path, $content);

		$fp = @fopen($path, "wb");
		if ($fp) {
			fwrite($fp, $content);
			fclose($fp);
		}
	}

	function _getFileContents($path) {
		$path = realpath($path);

		if (!$path || !@is_file($path))
			return "";

		if (function_exists("file_get_contents"))
			return @file_get_contents($path);

		$content = "";
		$fp = @fopen($path, "r");
		if (!$fp)
			return "";

		while (!feof($fp))
			$content .= fread($fp, 1024);

		fclose($fp);

		return $content;
	}

	function _strToItems($matches) {
		$this->_strings[] = $matches[0];

		return '¤@' . ($this->_count++) . '¤';
	}

	function _itemsToStr($matches) {
		return $this->_strings[intval($matches[1])];
	}

	function _encode($matches) {
		$this->_strings[] = $matches[0];

		return '¤#' . ($this->_count++) . '¤';
	}

	function _decode($matches) {
		return $this->_strings[intval($matches[1])];
	}

	function _parseTime($time) {
		$multipel = 1;

		// Hours
		if (strpos($time, "h") != false)
			$multipel = 60 * 60;

		// Days
		if (strpos($time, "d") != false)
			$multipel = 24 * 60 * 60;

		// Months
		if (strpos($time, "m") != false)
			$multipel = 24 * 60 * 60 * 30;

		// Trim string
		return intval($time) * $multipel;
	}

	/**#@-*/
}

?>