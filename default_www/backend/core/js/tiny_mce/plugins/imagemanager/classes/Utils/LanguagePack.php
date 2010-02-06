<?php
/**
 * $Id: LanguagePack.php 10 2007-05-27 10:55:12Z spocke $
 *
 * @package Moxiecode.utils
 * @author Moxiecode
 * @copyright Copyright © 2007, Moxiecode Systems AB, All rights reserved.
 */

/**
 * This class handles XML language packs.
 *
 * @package MCFileManager.utils
 */
class Moxiecode_LanguagePack {
	var $_items;
	var $_parser;
	var $_currentTarget;
	var $_currentLanguage;
	var $_currentDir;
	var $_encoding;
	var $_header;
	var $_tagContent;
	var $_tag;
	var $_tagAttrs;
	var $_cdataArr;

	function Moxiecode_LanguagePack() {
		$this->_header = array();
		$this->_items = array();
		$this->_cdataArr = array();

		$this->_currentTarget = "";
		$this->_currentLanguage = "en";
		$this->_currentDir = "ltr";
		
		$this->_header["major"] = 0;
		$this->_header["minor"] = 1;
		$this->_header["releasedate"] = date("Y-m-d");
	}

	function setLanguage($language) {
		$this->_currentLanguage = $language;
	}

	function getLanguage() {
		return $this->_currentLanguage;
	}

	/* Loads XML string */
	function loadXML($data) {
		// Check encoding on data
		preg_match('/<?xml.*encoding=[\'"](.*?)[\'"].*?>/m', $data, $matches);

		$encoding = "UTF-8";

		// Found XML encoding
		if (count($matches) > 1)
			$encoding = strtoupper($matches[1]);

		$this->_encoding = $encoding;

		// OMG! PHP Xpath is crappy, cant detect CDATA, haxx!
		$data = preg_replace("/<\!\[CDATA\[/", "<![CDATA[#CDATA#", $data);

		$this->_parser = xml_parser_create($encoding); // Auto detect for PHP4/PHP5
		xml_set_object($this->_parser, $this);
		xml_set_element_handler($this->_parser, "_saxStartElement", "_saxEndElement");
		xml_set_character_data_handler($this->_parser, "_saxCharacterData");
		xml_parser_set_option($this->_parser, XML_OPTION_TARGET_ENCODING, "UTF-8");

		if (!xml_parse($this->_parser, $data, true))
			trigger_error(sprintf("Language pack loading failed, XML error: %s at line %d.", xml_error_string(xml_get_error_code($this->_parser)), xml_get_current_line_number($this->_parser)), E_USER_ERROR);

		xml_parser_free($this->_parser);
	}

	/* Loads XML file */
	function load($file) {
		if (($fp = @fopen($file, "r"))) {
			$data = '';

			while (!feof($fp))
				$data .= fread($fp, 8192);

			fclose($fp);

			if (ini_get("magic_quotes_gpc"))
				$data = stripslashes($data);

			$this->loadXML($data);
		} else
			trigger_error("Could not open XML: ". $file, E_USER_ERROR);
	}

	function save($file, $enc="UTF-8") {
		if (($fp = @fopen($file, "w"))) {
			fwrite($fp, $this->toString($enc));
			fclose($fp);
		} else
			trigger_error("Could not open XML for writing: ". $file, E_USER_ERROR);
	}

	function getGroups() {
		return $this->_items[$this->_currentLanguage]["data"];
	}

	function setGroups($groups) {
		$this->_items[$this->_currentLanguage]["data"] = $groups;
	}

	function getGroup($name) {
		return $this->_items[$this->_currentLanguage]["data"][$name];
	}

	function get($target, $name) {
		return isset($this->_items[$this->_currentLanguage]["data"][$target][$name]) ? $this->_items[$this->_currentLanguage]["data"][$target][$name] : ("$" . $name . "$");
	}

	function set($target, $name, $value, $cdata = 3) {
		if ($cdata != 3) {
			if (!isset($this->_cdataArr[$this->_currentLanguage]))
				$this->_cdataArr[$this->_currentLanguage] = array();

			if (!isset($this->_cdataArr[$this->_currentLanguage][$target]))
				$this->_cdataArr[$this->_currentLanguage][$target] = array();

			$this->_cdataArr[$this->_currentLanguage][$target][$name] = $cdata;
		}

		if (!isset($this->_items[$this->_currentLanguage]["data"][$target]))
			$this->_items[$this->_currentLanguage]["data"][$target] = array();

		$this->_items[$this->_currentLanguage]["data"][$target][$name] = $value;
	}

	function getLanguageTitle() {
		return $this->_items[$this->_currentLanguage]["title"];
	}

	function setAuthor($author) {
		$this->_header["author"] = $author;
	}

	function getAuthor() {
		return $this->_header["author"];
	}

	function setVersion($major, $minor, $releasedate) {
		$this->_header["major"] = $major;
		$this->_header["minor"] = $minor;
		$this->_header["releasedate"] = $releasedate;
	}

	function getMinor() {
		return $this->_header["minor"];
	}

	function getMajor() {
		return $this->_header["major"];
	}

	function getReleaseDate() {
		return $this->_header["releasedate"];
	}

	function getDescription() {
		return $this->_header["description"];
	}

	function setDescription($description) {
		$this->_header["description"] = $description;
	}

	function createLanguage($lang, $dir, $title) {
		$this->_currentLanguage = $lang;
		$this->_items[$lang] = array("dir" => $dir, "title" => $title, "data" => array());
	}

	function updateLanguage($target, $lang, $dir, $title) {
		$this->_currentLanguage = $lang;
		$this->_items[$lang] = $this->_items[$target];
		unset($this->_items[$target]);
		$this->_items[$lang]["dir"] = $dir;
		$this->_items[$lang]["title"] = $title;
		$this->_cdataArr[$lang] = $this->_cdataArr[$target];
		unset($this->_cdataArr[$target]);		
	}

	// * * Private methods

	function _saxStartElement($parser, $name, $attrs) {
		$this->_tag = $name;
		$this->_tagAttrs = $attrs;
		$this->_tagContent = "";

		switch($name) {
			case "LANGUAGE":
				$this->_currentLanguage = $this->_tagAttrs["CODE"];
				$this->_currentDir = $this->_tagAttrs["DIR"];
				$this->_items[$this->_currentLanguage] = array("dir" => $this->_currentDir, "title" => $this->_tagAttrs["TITLE"], "data" => array());
			break;

			case "GROUP":
				$this->_currentTarget = $this->_tagAttrs["TARGET"];
				$this->_items[$this->_currentLanguage]["data"][$this->_tagAttrs["TARGET"]] = array();
			break;
		}
	}

	function _saxEndElement($parser, $name) {
		preg_match('/^#CDATA#/', $this->_tagContent, $matches);
		$this->_tagContent = preg_replace("/^#CDATA#/", "", $this->_tagContent);

		if (count($matches) == 0)
			$this->_tagContent = trim($this->_tagContent);

		switch($name) {
			case "ITEM":
				if (count($matches) != 0) {
					if (!isset($this->_cdataArr[$this->_currentLanguage]))
						$this->_cdataArr[$this->_currentLanguage] = array();

					if (!isset($this->_cdataArr[$this->_currentLanguage][$this->_currentTarget]))
						$this->_cdataArr[$this->_currentLanguage][$this->_currentTarget] = array();

					$this->_cdataArr[$this->_currentLanguage][$this->_currentTarget][$this->_tagAttrs["NAME"]] = true;
				}

				$this->_items[$this->_currentLanguage]["data"][$this->_currentTarget][$this->_tagAttrs["NAME"]] = $this->_tagContent;
			break;
			case "AUTHOR":
				$this->_header["author"] = $this->_tagContent;
			break;
			case "VERSION":
				$this->_header["minor"] = trim($this->_tagAttrs["MINOR"]);
				$this->_header["major"] = trim($this->_tagAttrs["MAJOR"]);
				$this->_header["releasedate"] = trim($this->_tagAttrs["RELEASEDATE"]);
			break;
			case "DESCRIPTION":
				$this->_header["description"] = $this->_tagContent;
			break;
		}

		// Clear memory!
		$this->_tagContent = "";
		$this->_tag = "";
		$this->_tagAttrs = "";
	}

	function _saxCharacterData($parser, $data) {
		$this->_tagContent .= $data;
	}

	function xmlEncode($str) {
		if (strtolower($this->_encoding) == "utf-8")
			return utf8_encode(htmlspecialchars($str, ENT_QUOTES, $this->_encoding));

		return htmlspecialchars($str, ENT_QUOTES, $this->_encoding);
	}

	function toString($enc = "") {
		$oldenc = "";

		if ($enc == "")
			$enc = $this->_encoding;
		else {
			$oldenc = $this->_encoding;
			$this->_encoding = $enc;
		}

		$doc = "";
		$doc .= '<?xml version="1.0" encoding="'. $enc .'"?>'. "\n";
		$doc .= '<language-pack>'. "\n";
		$doc .= '	<header>'. "\n";
		$doc .= '		<author>'. $this->xmlEncode($this->_header["author"]) .'</author>'. "\n";
		$doc .= '		<version major="'. $this->xmlEncode($this->_header["major"]) .'" minor="'. $this->xmlEncode($this->_header["minor"]) .'" releasedate="'. $this->xmlEncode($this->_header["releasedate"]) .'" />'. "\n";
		$doc .= '		<description>'. $this->xmlEncode($this->_header["description"]) .'</description>'. "\n";
		$doc .= '	</header>'. "\n";

		foreach($this->_items as $code => $language) {
			$doc .= '	<language code="'. $this->xmlEncode($code) .'" dir="'. $this->xmlEncode($language["dir"]) .'" title="'. $this->xmlEncode($language["title"]) .'">'. "\n";
			foreach($language["data"] as $target => $group) {
				$doc .= '		<group target="'. $this->xmlEncode($target) .'">'. "\n";

				foreach($group as $name => $item) {
					if (isset($this->_cdataArr[$code]) && isset($this->_cdataArr[$code][$target]) && isset($this->_cdataArr[$code][$target][$name]) && $this->_cdataArr[$code][$target][$name]) {
						$doc .= '			<item name="'. $this->xmlEncode($name) .'"><![CDATA['. $item .']]></item>'. "\n";
					} else
						$doc .= '			<item name="'. $this->xmlEncode($name) .'">'. $this->xmlEncode($item) .'</item>'. "\n";
				}
				$doc .= '		</group>'. "\n";
			}
			$doc .= '	</language>'. "\n";
		}

		$doc .= '</language-pack>'. "\n";

		if ($oldenc != "")
			$this->_encoding = $oldenc;

		return $doc;
	}
}
?>