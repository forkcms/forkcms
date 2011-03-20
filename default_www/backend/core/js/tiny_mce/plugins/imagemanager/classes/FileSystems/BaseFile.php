<?php
/**
 * $Id: BaseFile.php 756 2009-11-26 15:57:57Z spocke $
 *
 * @package MCFileManager.filesystems
 * @author Moxiecode
 * @copyright Copyright © 2005, Moxiecode Systems AB, All rights reserved.
 */

// File type contstants
define('MC_IS_UNKNOWN', -1);
define('MC_IS_FILE', 0);
define('MC_IS_DIRECTORY', 1);

// Action constants
define('DELETE_ACTION', 1);
define('ADD_ACTION', 2);
define('UPDATE_ACTION', 3);
define('RENAME_ACTION', 4);
define('COPY_ACTION', 5);
define('MKDIR_ACTION', 6);
define('RMDIR_ACTION', 7);

/**
 * This class is the base class for files and is to be extended by all FileSystem implementations.
 *
 * @package MCManager.filesystems
 */
class Moxiecode_BaseFile {
	/**
	 * Set a bool regarding events triggering.
	 *
	 * @param Bool $trigger Trigger or not to trigger.
	 */
	function setTriggerEvents($trigger) {
	}

	/**
	 * Returns bool if events are to be triggered or not.
	 *
	 * @return Bool bool for triggering events or not.
	 */
	function getTriggerEvents() {
	}

	/**
	 * Returns the parent files absolute path.
	 *
	 * @return String parent files absolute path.
	 */
	function getParent() {
	}

	/**
	 * Returns the parent files MCE_File instance.
	 *
	 * @return MCE_File parent files MCE_File instance or false if there is no more parents.
	 */
	function &getParentFile() {
	}

	/**
	 * Returns the file name of a file.
	 *
	 * @return string File name of file.
	 */
	function getName() {
	}

	/**
	 * Returns the absolute path of the file.
	 *
	 * @return String absolute path of the file.
	 */
	function getAbsolutePath() {
	}

	/**
	 * Imports a local file to the file system, for example when users upload files.
	 * Implementations of this method should also support directory recursive importing.
	 *
	 * @param String $local_absolute_path Absolute path to local file.
	 * @return true/false state if it was imported or not.
	 */
	function importFile($local_absolute_path) {
		return false;
	}

	/**
	 * Exports the file to the local system, for example a file from a zip or db file system.
	 * Implementations of this method should also support directory recursive exporting.
	 *
	 * @param String $local_absolute_path Absolute path to local file.
	 * @return true/false state if it was exported or not.
	 */
	function exportFile($local_absolute_path) {
		return false;
	}

	/**
	 * Returns true if the file exists.
	 *
	 * @return boolean true if the file exists.
	 */
	function exists() {
	}

	/**
	 * Returns true if the file is a directory.
	 *
	 * @return boolean true if the file is a directory.
	 */
	function isDirectory() {
	}

	/**
	 * Returns true if the file is a file.
	 *
	 * @return boolean true if the file is a file.
	 */
	function isFile() {
	}

	/**
	 * Returns true if the files is readable.
	 *
	 * @return boolean true if the files is readable.
	 */
	function canRead() {
	}
	
	/**
	 * Returns true if the files is writable.
	 *
	 * @return boolean true if the files is writable.
	 */
	function canWrite() {
	}

	/**
	 * Returns file size as an long.
	 *
	 * @return long file size as an long.
	 */
	function getLength() {
	}

	/**
	 * Copies this file to the specified file instance.
	 *
	 * @param MCE_File $dest File to copy to.
	 * @return boolean true - success, false - failure
	 */
	function copyTo(&$dest) {
	}

	/**
	 * Deletes the file.
	 *
	 * @param boolean $deep If this option is enabled files will be deleted recurive.
	 * @return boolean true - success, false - failure
	 */
	function delete($deep = false) {
	}

	/**
	 * Returns an array of MCE_File instances.
	 *
	 * @return Array array of MCE_File instances.
	 */
	function &listFiles() {
	}

	/**
	 * Returns an array of MCE_File instances based on the specified filter instance.
	 *
	 * @param MCE_FileFilter &$filter MCE_FileFilter instance to filter files by.
	 * @return Array array of MCE_File instances based on the specified filter instance.
	 */
	function &listFilesFiltered(&$filter) {
	}

	/**
	 * Lists the file as an tree and calls the specified MCE_FileTreeHandler instance on each file. 
	 *
	 * @param MCE_FileTreeHandler &$file_tree_handler MCE_FileTreeHandler to invoke on each file.
	 */
	function listTree(&$file_tree_handler) {
	}

	/**
	 * Lists the file as an tree and calls the specified MCE_FileTreeHandler instance on each file
	 * if the file filter accepts the file.
	 *
	 * @param MCE_FileTreeHandler &$file_tree_handler MCE_FileTreeHandler to invoke on each file.
	 * @param MCE_FileTreeHandler &$file_filter MCE_FileFilter instance to filter files by.
	 */
	function listTreeFiltered(&$file_tree_handler, &$file_filter) {
	}

	/**
	 * Creates a new directory.
	 *
	 * @return boolean true- success, false - failure
	 */
	function mkdir() {
	}

	/**
	 * Renames/Moves this file to the specified file instance.
	 *
	 * @param MCE_File $dest File to rename/move to.
	 * @return boolean true- success, false - failure
	 */
	function renameTo(&$dest) {
	}

	/**
	 * Sets the last-modified time of the file or directory.
	 *
	 * @param String $datetime The new date/time to set the file, in timestamp format
	 * @return boolean true - success, false - failure
	 */
	function setLastModified($datetime) {
	}

	/**
	 * Returns last modification date in ms as an long.
	 *
	 * @return long last modification date in ms as an long.
	 */
	function getLastModified() {
	}

	/**
	 * Returns creation date in ms as an long.
	 *
	 * @return long creation date in ms as an long.
	 */
	function getCreationDate() {
	}

	/**
	 * Returns a merged name/value array of config elements.
	 *
	 * @return Array Merged name/value array of config elements.
	 */
	function getConfig() {
	}

	/**
	 * Opens a file stream by the specified mode. The default mode is rb.
	 *
	 * @param String $mode Mode to open file by, r, rb, w, wb etc.
	 * @return FileStream File stream implementation for the file system.
	 */
	function &open($mode = 'rb') {
	}

	/**
	 * Returns true/false if the file system has a command implemented or not.
	 *
	 * @param string $cmd Command to check for.
	 * @return bool true/false if the command is implemented or not.
	 */
	function hasCommand($cmd) {
		return false;
	}

	/**
	 * Executes a specific command. Commands makes it possible for file system specific actions.
	 *
	 * @param string $cmd Command to execute.
	 * @param string $args Optional command argument.
	 * @return mixed Returns any object or primary data type that the command need to return.
	 */
	function execCommand($cmd, $arg = false) {
		return false;
	}
}

?>