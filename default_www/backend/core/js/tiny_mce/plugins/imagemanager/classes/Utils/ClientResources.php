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

class Moxiecode_ClientResources {
	/**#@+ @access private */

	var $_path, $_files, $_settings, $_debug;

	/**#@-*/

	function Moxiecode_ClientResources($settings = array()) {
		$default = array(
		);

		$this->_settings = array_merge($default, $settings);
		$this->_files = array();
	}

	function isDebugEnabled() {
		return $this->_debug;
	}

	function getSetting($name, $default = false) {
		return isset($this->_settings["name"]) ? $this->_settings["name"] : $default;
	}

	function getPackageIDs() {
		return array_keys($this->_files);
	}

	function &getFile($package, $file_id) {
		$files = $this->getFiles($package);

		foreach ($files as $file) {
			if ($file->getId() == $file_id)
				return $file;
		}

		return null;
	}

	function getFiles($package) {
		return isset($this->_files[$package]) ? $this->_files[$package] : array();
	}

	function load($xml_file) {
		$this->_path = dirname($xml_file);

		if (!file_exists($xml_file))
			return;

		$fp = @fopen($xml_file, "r");
		if ($fp) {
			$data = '';

			while (!feof($fp))
				$data .= fread($fp, 8192);

			fclose($fp);

			if (ini_get("magic_quotes_gpc"))
				$data = stripslashes($data);
		}

		$this->_parser = xml_parser_create('UTF-8');
		xml_set_object($this->_parser, $this);
		xml_set_element_handler($this->_parser, "_saxStartElement", "_saxEndElement");
		xml_parser_set_option($this->_parser, XML_OPTION_TARGET_ENCODING, "UTF-8");

		if (!xml_parse($this->_parser, $data, true))
			trigger_error(sprintf("Language pack loading failed, XML error: %s at line %d.", xml_error_string(xml_get_error_code($this->_parser)), xml_get_current_line_number($this->_parser)), E_USER_ERROR);

		xml_parser_free($this->_parser);
	}

	// * * Private methods

	function _saxStartElement($parser, $name, $attrs) {
		switch ($name) {
			case "RESOURCES":
				if (!$this->_debug)
					$this->_debug = isset($attrs["DEBUG"]) && $attrs["DEBUG"] == 'yes';

				break;

			case "PACKAGE":
				$this->_packageID = isset($attrs["ID"]) ? $attrs["ID"] : 'noid';

				if (!isset($this->_files[$this->_packageID]))
					$this->_files[$this->_packageID] = array();
			break;

			case "FILE":
				$this->_files[$this->_packageID][] = new Moxiecode_ClientResourceFile(
					isset($attrs["ID"]) ? $attrs["ID"] : "",
					str_replace("\\", DIRECTORY_SEPARATOR, $this->_path . '/' . $attrs["PATH"]),
					!isset($attrs["KEEPWHITESPACE"]) || $attrs["KEEPWHITESPACE"] != "yes",
					isset($attrs["TYPE"]) ? $attrs["TYPE"] : ''
				);
			break;
		}
	}

	function _saxEndElement($parser, $name) {
	}
}

class Moxiecode_ClientResourceFile {
	/**#@+ @access private */

	var $_id, $_contentType, $_path, $_remove_whitespace;

	/**#@-*/

	function Moxiecode_ClientResourceFile($id, $path, $remove_whitespace, $content_type) {
		$this->_id = $id;
		$this->_path = $path;
		$this->_remove_whitespace = $remove_whitespace;
		$this->_contentType = $content_type;
	}

	function isRemoveWhitespaceEnabled() {
		return $this->_remove_whitespace;
	}

	function getId() {
		return $this->_id;
	}

	function getContentType() {
		return $this->_contentType;
	}

	function getPath() {
		return $this->_path;
	}
}

?>