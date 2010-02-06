<?php
/**
 * $Id: FileTreeHandler.php 149 2007-11-06 11:24:58Z spocke $
 *
 * @package MCFileManager.filesystems
 * @author Moxiecode
 * @copyright Copyright © 2005, Moxiecode Systems AB, All rights reserved.
 */

/**
 * This class is the base FileTreeHandler class and is to be extended by all custom FileTreeHandler implementations.
 *
 * @package mce.core
 */
class Moxiecode_FileTreeHandler {
	/**
	 * Continue tree search.
	 *
	 * @var int $CONTINUE 
	 */
	var $CONTINUE = 1;

	/**
	 * Abort tree search.
	 *
	 * @var int $ABORT 
	 */
	var $ABORT = 2;

	/**
	 * Abort tree search on the current folder/directory.
	 *
	 * @var int $ABORT_FOLDER 
	 */
	var $ABORT_FOLDER = 3;

	/**
	 * Handles a file instance while looping an tree of directories.
	 * 
	 * @param MCE_File $file File object reference
	 * @param int $level Current level of tree parse
	 * @return int State of what to do next can be CONTINUE, ABORT or ABORTFOLDER.
	 */
	function handle($file, $level) {
		// code here...
	}
}

/**
 * Basic file tree handler, this class handles some common file tree problems
 * and is possible to extend if needed.
 *
 * @package mce.core
 */
class Moxiecode_BasicFileTreeHandler extends Moxiecode_FileTreeHandler {
	/**#@+
	 * @access private
	 */

	var $_maxLevel;
	var $_makeArray;
	var $_array;
	var $_onlyFiles;
	var $_onlyDirs;

	/**#@+
	 * @access public
	 */

	/**
	 * Main constructor.
	 */
	function Moxiecode_BasicFileTreeHandler() {
		$this->_onlyFiles = false;
		$this->_onlyDirs = false;
	}

	/**
	 * Sets only files mode.
	 *
	 * @param bool $only_files true if only files should be added to output.
	 */
	function setOnlyFiles($only_files) {
		$this->_onlyFiles = $only_files;
	}

	/**
	 * Gets only files mode.
	 *
	 * @return bool true if only files should be added to output.
	 */
	function getOnlyFiles() {
		return $this->_onlyFiles;
	}

	/**
	 * Sets only dirs mode.
	 *
	 * @param bool $only_dirs true if only dirs should be added to output.
	 */
	function setOnlyDirs($only_dirs) {
		$this->_onlyDirs = $only_dirs;
	}

	/**
	 * Gets only dirs mode.
	 *
	 * @return bool true if only dirs should be added to output.
	 */
	function getOnlyDirs() {
		return $this->_onlyDirs;
	}

	/**
	 * Sets the max level to include in tree parse.
	 *
	 * @param int $level max level to include in tree parse.
	 */
	function setMaxLevel($level) {
		$this->_maxLevel = $level;
	}

	/**
	 * Sets is the result is to be built in to an array or not.
	 * The result is returned by getFileArray when the tree list completes.
	 * Note: The default value of this state is false.
	 *
	 * @param boolean $state True if the result is to be built in to an array or not.
	 */
	function setMakeArray($state) {
		$this->_makeArray = $state;
		if ($this->_makeArray)
			$this->_array = array();
	}

	/**
	 * Returns the tree as an array of MCE_File instances. This method
	 * will not return anything if the setMakeArray isn't set to true.
	 *
	 * @return Array Array of MCE_File instances.
	 */
	function &getFileArray() {
		return $this->_array;
	}

	/**
	 * Handles a file instance while looping an tree of directories.
	 * 
	 * @param MCE_File $file File object reference
	 * @param int $level Current level of tree parse
	 * @return int State of what to do next can be CONTINUE, ABORT or ABORTFOLDER.
	 */
	function handle($file, $level) {
		$add = true;

		if (is_array($this->_array) && $this->_onlyDirs && $file->isFile())
			$add = false;

		if (is_array($this->_array) && $this->_onlyFiles && $file->isDir())
			$add = false;

		if ($add)
			$this->_array[] = $file;

		if ($this->_maxLevel && $level >= $this->_maxLevel)
			return $this->ABORT_FOLDER;

		return $this->CONTINUE;
	}

	/**#@-*/
}

class Moxiecode_ConfigFilteredFileTreeHandler extends Moxiecode_BasicFileTreeHandler {
	var $_config;

	/**
	 * Handles a file instance while looping an tree of directories.
	 * 
	 * @param MCE_File $file File object reference
	 * @param int $level Current level of tree parse
	 * @return int State of what to do next can be CONTINUE, ABORT or ABORTFOLDER.
	 */
	function handle($file, $level) {
		if ($file->isDirectory() || !is_array($this->_config)) {
			if ($level == 0)
				return parent::handle($file, $level);
			else
				$parentFile = $file->getParentFile();

			$this->_config = $parentFile->getConfig();
		}

		$filter = new Moxiecode_BasicFileFilter();

		$filter->setIncludeFilePattern($this->_config['filesystem.include_file_pattern']);
		$filter->setExcludeFilePattern($this->_config['filesystem.exclude_file_pattern']);

		$filter->setIncludeDirectoryPattern($this->_config['filesystem.include_directory_pattern']);
		$filter->setExcludeDirectoryPattern($this->_config['filesystem.exclude_directory_pattern']);

		$filter->setOnlyDirs($this->_onlyDirs);

		if (!$filter->accept($file))
			return $this->ABORT_FOLDER;

		return parent::handle($file, $level);
	}
}

?>