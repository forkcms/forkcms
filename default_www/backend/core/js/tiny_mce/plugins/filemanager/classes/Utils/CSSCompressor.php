<?php
/**
 * Moxiecode CSS Compressor.
 *
 * @author Moxiecode
 * @site http://www.moxieforge.com/
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 * @licence LGPL
 * @ignore
 */

/**
 * This class is used to compress CSS files by minifying and gzipping them to reduce the overall download time for the CSS of a site or system.
 *
 * @package Moxiecode_Utils
 */
class Moxiecode_CSSCompressor {
	/**#@+ @access private */

	var $_items, $_settings, $_lastUpdate;

	/**#@-*/

	/**
	 * Constructs a new CSS compressor instance.
	 *
	 * @param Array $settings Name/value array with settings for the compressor instance.
	 */
	function Moxiecode_CSSCompressor($settings = array()) {
		$this->_items = array();

		$default = array(
			'expires_offset' => '10d',
			'disk_cache' => true,
			'cache_dir' => '_cache',
			'gzip_compress' => true,
			'remove_whitespace' => true,
			'charset' => 'UTF-8',
			'convert_urls' => true,
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
			$cacheFile .= ".css";

		// Set headers
		header("Content-type: text/css;charset=" . $this->_settings['charset']);
		header("Cache-Control: must-revalidate"); // Must be there for IE 6
		header("Vary: Accept-Encoding");  // Handle proxies
		header("Expires: " . gmdate("D, d M Y H:i:s", time() + $this->_parseTime($this->_settings['expires_offset'])) . " GMT");

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

			if (!preg_match('/[\r\n]$/', $chunk))
				$chunk .= "\n";

			if ($this->_settings['remove_whitespace'] && $item[2])
				$chunk = $this->_removeWhiteSpace($chunk);

			// Convert urls
			if ($this->_settings['convert_urls']) {
				$chunk = preg_replace('/\\$base/', dirname($_SERVER['SCRIPT_NAME']), $chunk);
				$chunk = preg_replace('/url\\([\'"]?(?!\\/|http)/', '$0' . dirname($item[1]) . '/', $chunk);
			}

			$content .= $chunk;
		}

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
		// Remove comments
		$content = preg_replace('/(\\/\\*[^*]*\\*+([^\\/][^*]*\\*+)*\\/)/', '', $content);

		// Remove whitespace at the beginning and end of CSS
		$content = preg_replace('/^[\r\n]+/', '', $content);
		$content = preg_replace('/[\r\n]+$/', "\n", $content);

		// Remove redundant linebreaks
		$content = preg_replace('/\r\n/', "\n", $content);
		$content = preg_replace('/\n+/', "\n", $content);

		// Remove whitespace before/after styles inside rules
		$content = preg_replace('/\\{\\s*(.*?)\\s*\\}/', '{$1}', $content);

		// Remove remove whitespace between style rules and after the last one
		$content = preg_replace('/;\\s+/', ';', $content);
		$content = preg_replace('/\\{([^\\}]+);\\}/', '{$1}', $content);

		// Remove whitespace between :
		$content = preg_replace('/\\s*\\:\\s*/', ':', $content);

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