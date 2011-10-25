<?php
/**
 * $Id: ResultSet.php 10 2007-05-27 10:55:12Z spocke $
 *
 * @package MCManager.utils
 * @author Moxiecode
 * @copyright Copyright © 2007, Moxiecode Systems AB, All rights reserved.
 */

/**
 * This class handles tablular resultsets like a database table.
 *
 * @package MCManager.utils
 */
class Moxiecode_ResultSet {
	var $_cols;
	var $_rows;
	var $_header;
	var $_config;

	function Moxiecode_ResultSet($cols, $header = array()) {
		$this->_cols = explode(',', $cols);
		$this->_rows = array();
		$this->_config = null;
	}

	function add() {
		$this->_rows[] = func_get_args();
	}

	function setHeader($name, $value) {
		$this->_header[$name] = $value;
	}

	function setConfig($config) {
		$this->_config = $config;
	}

	function getRowCount() {
		return count($this->_rows);
	}

	function getRows() {
		$rowsArr = array();
		
		for ($i=0; $i<count($this->_rows);$i++)
			$rowsArr[] = $this->getRow($i);

		return $rowsArr;
	}

	function getRow($index) {
		if ($index < 0)
			return null;

		$row = $this->_rows[$index];

		$obj = array();

		for ($i=0; $i<count($row);$i++)
			$obj[$this->_cols[$i]] = $row[$i];

		return $obj;
	}

	function toArray() {
		$ar = array(
			"header" => $this->_header,
			"columns" => $this->_cols,
			"data" => $this->_rows
		);

		if (is_array($this->_config))
			$ar["config"] = $this->_config;

		return $ar;
	}
}

?>