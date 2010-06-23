<?php
/**
 * $Id: BaseFileImpl.php 10 2007-05-27 10:55:12Z spocke $
 *
 * @package MCFileManager.filesystems
 * @author Moxiecode
 * @copyright Copyright © 2005, Moxiecode Systems AB, All rights reserved.
 */

/**
 * Implements some of the basic features of a FileSystem but not specific functionality.
 *
 * @package MCFileManager.filesystems
 */
class Moxiecode_BaseFileImpl extends Moxiecode_BaseFile {
	// Private fields
	var $_absPath;
	var $_type;
	var $_manager;
	var $_events = true;

	/**
	 * Creates a new absolute file.
	 *
	 * @param MCManager $manager MCManager reference.
	 * @param String $absolute_path Absolute path to local file.
	 * @param String $child_name Name of child file (Optional).
	 * @param String $type Optional file type.
	 */
	function Moxiecode_BaseFileImpl(&$manager, $absolute_path, $child_name = "", $type = MC_IS_FILE) {
		$this->_manager =& $manager;
		$this->_type = $type;

		if ($child_name != "")
			 $this->_absPath = $this->_manager->removeTrailingSlash($absolute_path) . "/" . $child_name;
		else
			$this->_absPath = $absolute_path;
	}

	/**
	 * Set a bool regarding events triggering.
	 *
	 * @param Bool $trigger Trigger or not to trigger.
	 */
	function setTriggerEvents($trigger) {
		$this->_events = $trigger;
	}

	/**
	 * Returns bool if events are to be triggered or not.
	 *
	 * @return Bool bool for triggering events or not.
	 */
	function getTriggerEvents() {
		return $this->_events;
	}

	/**
	 * Returns the parent files absolute path.
	 *
	 * @return String parent files absolute path.
	 */
	function getParent() {
		$pathAr = explode("/", $this->getAbsolutePath());

		array_pop($pathAr);
		$path = implode("/", $pathAr);

		return ($path == "") ? "/" : $path;
	}

	/**
	 * Returns the file name of a file.
	 *
	 * @return string File name of file.
	 */
	function getName() {
		return basename($this->_absPath);
	}

	/**
	 * Returns the absolute path of the file.
	 *
	 * @return String absolute path of the file.
	 */
	function getAbsolutePath() {
		return $this->_absPath;
	}

	/**
	 * Returns true if the file is a directory.
	 *
	 * @return boolean true if the file is a directory.
	 */
	function isDirectory() {
		if (!$this->exists())
			return $this->_type == MC_IS_DIRECTORY;

		return is_dir($this->_manager->toOSPath($this->_absPath));
	}

	/**
	 * Returns true if the file is a file.
	 *
	 * @return boolean true if the file is a file.
	 */
	function isFile() {
		if (!$this->exists())
			return $this->_type == MC_IS_FILE;

		return !$this->isDirectory();
	}

	/**
	 * Returns an array of File instances.
	 *
	 * @return Array array of File instances.
	 */
	function &listFiles() {
		$files = $this->listFilesFiltered(new Moxiecode_DummyFileFilter());
		return $files;
	}

	/**
	 * Lists the file as an tree and calls the specified FileTreeHandler instance on each file. 
	 *
	 * @param FileTreeHandler &$file_tree_handler FileTreeHandler to invoke on each file.
	 */
	function listTree(&$file_tree_handler) {
		$this->_listTree($this, $file_tree_handler, new Moxiecode_DummyFileFilter(), 0);
	}

	/**
	 * Lists the file as an tree and calls the specified FileTreeHandler instance on each file
	 * if the file filter accepts the file.
	 *
	 * @param FileTreeHandler &$file_tree_handler FileTreeHandler to invoke on each file.
	 * @param FileTreeHandler &$file_filter FileFilter instance to filter files by.
	 */
	function listTreeFiltered(&$file_tree_handler, &$file_filter) {
		$this->_listTree($this, $file_tree_handler, $file_filter, 0);
	}

	// * * Private methods

	/**
	 * Lists files recursive, and places the files in the specified array.
	 */
	function _listTree($file, &$file_tree_handler, &$file_filter, $level) {
		$state = $file_tree_handler->CONTINUE;

		if ($file_filter->accept($file)) {
			$state = $file_tree_handler->handle($file, $level);

			if ($state == $file_tree_handler->ABORT || $state == $file_tree_handler->ABORT_FOLDER)
				return $state;
		}

		$files = $file->listFiles();

		foreach ($files as $file) {
			if ($file_filter->accept($file)) {
				if ($file->isFile()) {
					// This is some weird shit!
					//if (!is_object($file_filter))
						$state = $file_tree_handler->handle($file, $level);
				} else {
					$state = $this->_listTree($file, $file_tree_handler, $file_filter, ++$level);
					--$level;
				}
			}

			if ($state == $file_tree_handler->ABORT)
				return $state;
		}

		return $file_tree_handler->CONTINUE;
	}
}

?>