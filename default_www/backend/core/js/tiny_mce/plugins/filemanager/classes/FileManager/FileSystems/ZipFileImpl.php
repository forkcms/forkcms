<?php
/**
 * $Id: ZipFileImpl.php 751 2009-10-20 12:05:36Z spocke $
 *
 * @package MCFileManager.filesystems
 * @author Moxiecode
 * @copyright Copyright © 2005, Moxiecode Systems AB, All rights reserved.
 */

require_once(MCMANAGER_ABSPATH . "FileManager/Utils/ZipFile.class.php");

/**
 * This is the zip file system implementation of BaseFile.
 *
 * @package MCFileManager.filesystems
 */
class Moxiecode_ZipFileImpl extends Moxiecode_BaseFileImpl {
	// Private fields
	var $_config;
	var $_zipEntry;
	var $_zipPath;
	var $_innerPath;

	/**
	 * Creates a new absolute file.
	 *
	 * @param MCManager $manager MCManager reference.
	 * @param String $absolute_path Absolute path to local file.
	 * @param String $child_name Name of child file (Optional).
	 * @param String $type Optional file type.
	 */
	function Moxiecode_ZipFileImpl(&$manager, $absolute_path, $child_name = "", $type = MC_IS_FILE) {
		Moxiecode_BaseFileImpl::Moxiecode_BaseFileImpl($manager, $absolute_path, $child_name, $type);

		$matches = array();
		$this->_absPath = str_replace("zip://", "", $this->_absPath);
		preg_match("/^(.*?.zip)(.*?)$/i", $this->_absPath, $matches);

		$this->_zipPath = $matches[1];
		$this->_innerPath = $matches[2];

		if (!$this->_innerPath)
			$this->_innerPath = "/";
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
		return "zip://" . $this->_absPath;
	}

	/**
	 * Returns the parent files absolute path.
	 *
	 * @return String parent files absolute path.
	 */
	function getParent() {
		// If get parent on root the give it the local FS parent
		if ($this->_innerPath == "/") {
			$file = $this->_manager->getFile($this->_zipPath);
			return $file->getParent();
		}

		return Moxiecode_BaseFileImpl::getParent();
	}

	/**
	 * Returns the parent files File instance.
	 *
	 * @return File parent files File instance or false if there is no more parents.
	 */
	function &getParentFile() {
		if ($this->_innerPath == "/")
			return $this->_manager->getFile($this->getParent());

		$file = new Moxiecode_ZipFileImpl($this->_manager, $this->getParent());

		return $file;
	}

	/**
	 * Imports a local file to the file system, for example when users upload files.
	 *											   
	 * @param String $local_absolute_path Absolute path to local file to import.
	 */
	function importFile($local_absolute_path = "") {
		$zip =& $this->_getZip();

		$zip->open();

		if (is_file($local_absolute_path))
			$zip->addFile($this->_innerPath, $local_absolute_path);
		else
			$zip->addDirectory($this->_innerPath, $local_absolute_path);

		$status = $zip->commit();
		$zip->close();

		return $status;
	}

	/**
	 * Exports the file to the local system, for example a file from a zip or db file system.
	 *
	 * @param String $local_absolute_path Absolute path to local file.
	 * @return true/false state if it was exported or not.
	 */
	function exportFile($local_absolute_path) {
		$zip =& $this->_getZip();

		$zip->open();
		$zip->extract($this->_innerPath, $local_absolute_path, true);
		$zip->close();

		return true;
	}

	/**
	 * Returns true if the file exists.
	 *
	 * @return boolean true if the file exists.
	 */
	function exists() {
		// If zip root
		if ($this->_innerPath == "/")
			return true;

		// Look for item inside zip
		$entry =& $this->_getZipEntry();

		return $entry != null;
	}

	/**
	 * Returns true if the file is a directory.
	 *
	 * @return boolean true if the file is a directory.
	 */
	function isDirectory() {
		// If zip root
		if ($this->_innerPath == "/")
			return true;

		if (!$this->exists())
			return $this->_type == MC_IS_DIRECTORY;

		$entry =& $this->_getZipEntry();

		return $entry->isDirectory();
	}

	/**
	 * Returns last modification date in ms as an long.
	 *
	 * @return long last modification date in ms as an long.
	 */
	function getLastModified() {
		$entry =& $this->_getZipEntry();

		return $entry->getLastModified();
	}

	/**
	 * Returns creation date in ms as an long.
	 *
	 * @return long creation date in ms as an long.
	 */
	function getCreationDate() {
		$entry =& $this->_getZipEntry();

		return $entry->getLastModified();
	}

	/**
	 * Returns true if the files is readable.
	 *
	 * @return boolean true if the files is readable.
	 */
	function canRead() {
		return true;
	}

	/**
	 * Returns true if the files is writable.
	 *
	 * @return boolean true if the files is writable.
	 */
	function canWrite() {
		return true;
	}

	/**
	 * Returns file size as an long.
	 *
	 * @return long file size as an long.
	 */
	function getLength() {
		$entry =& $this->_getZipEntry();

		return $entry->getSize();
	}

	/**
	 * Copies this file to the specified file instance.
	 *
	 * @param File $dest File to copy to.
	 * @return boolean true - success, false - failure
	 */
	function copyTo(&$dest) {
		if (getClassName($dest) == 'moxiecode_localfileimpl')
			return $this->exportFile($dest->getAbsolutePath());

		if ($this->isDirectory()) {
			$handle_as_add_event = true;
			$treeHandler = new _ZipFileCopyDirTreeHandler($this->_manager, $this, $dest, $handle_as_add_event);

			$this->listTree($treeHandler);

			return true;
		} else {
			// From/To Zip
			if (getClassName($dest) == 'moxiecode_zipfileimpl') {
				$zip =& $this->_getZip();
				$toZip =& $dest->_getZip();

				$zip->open();
				$toZip->open();

				// Copy single file
				$entry =& $zip->getEntryByPath($this->_innerPath);
				if ($entry && $entry->isFile()) {
					$toZip->addData($dest->_innerPath, $entry->getData());
				} else {
					// Copy directory
					$entries =& $zip->listEntries($this->_innerPath, true);
					for ($i=0; $i<count($entries); $i++) {
						$entry =& $entries[$i];

						if ($entry->isFile())
							$toZip->addData($dest->_innerPath, $entry->getData());
						else
							$toZip->addDirectory($dest->_innerPath);
					}
				}

				$zip->close();
				$toZip->commit();
				$toZip->close();
			} else {
				// Copy non local fs file
				$inStream =& $this->open('rb');
				$outStream =& $dest->open('wb');

				if ($inStream && $outStream) {
					while (($buff = $inStream->read()) != null)
						$outStream->write($buff);
				}

				if ($inStream)
					$inStream->close();

				if ($outStream)
					$outStream->close();
			}
		}

		return true;
	}

	/**
	 * Deletes the file.
	 *
	 * @param boolean $deep If this option is enabled files will be deleted recurive.
	 * @return boolean true - success, false - failure
	 */
	function delete($deep = false) {
		$zip =& $this->_getZip();

		$zip->open();

		if ($this->isFile())
			$state = $zip->deleteFile($this->_innerPath);
		else
			$state = $zip->deleteDir($this->_innerPath, $deep);

		$zip->commit();
		$zip->close();

		return $state;
	}

	/**
	 * Returns an array of BaseFile instances based on the specified filter instance.
	 *
	 * @param FileFilter &$filter FileFilter instance to filter files by.
	 * @return Array array of File instances based on the specified filter instance.
	 */
	function &listFilesFiltered(&$filter) {
		$dirs = array();
		$files = array();
		$zip =& $this->_getZip();
		$entries = $zip->listEntries($this->_innerPath);

		// Dirs
		foreach ($entries as $entry) {
			$zipFile = new Moxiecode_ZipFileImpl($this->_manager, $this->_absPath, $entry->getName());

			$zipFile->_setZipEntry($entry);

			if ($filter->accept($zipFile) == BASIC_FILEFILTER_ACCEPTED) {
				if ($zipFile->isFile())
					$files[] = $zipFile;
				else
					$dirs[] = $zipFile;
			}
		}

		$zip->close();

		$ar = array_merge($dirs, $files); // Stupid PHP 5 notices

		return $ar;
	}

	/**
	 * Creates a new directory.
	 *
	 * @return boolean true- success, false - failure
	 */
	function mkdir() {
		$zip =& $this->_getZip();

		$zip->open();

		$state = $zip->addDirectory($this->_innerPath);

		$zip->commit();
		$zip->close();

		return $state;
	}

	/**
	 * Renames/Moves this file to the specified file instance.
	 *
	 * @param File $dest File to rename/move to.
	 * @return boolean true- success, false - failure
	 */
	function renameTo(&$dest) {
		// If move within the same zip
		if (getClassName($dest) == 'moxiecode_zipfileimpl' && $this->_zipPath == $dest->_zipPath) {
			$zip =& $this->_getZip();
			$zip->open();
			$zip->moveEntry($this->_innerPath, $dest->_innerPath);
			$zip->commit();
			$zip->close();
		} else {
			// Copy and delete
			$this->copyTo($dest);
			$this->delete(true);
		}

		return true;
	}

	/*
	 * Sets the last-modified time of the file or directory.
	 *
	 * @param String $datetime The new date/time to set the file, in timestamp format
	 * @return boolean true- success, false - failure
	 */
	function setLastModified($datetime) {
		$zip =& $this->_getZip();

		$zip->open();

		// Check for file
		$entry =& $zip->getEntryByPath($this->_innerPath);

		// Check for dir
		if (!$entry)
			$entry =& $zip->getEntryByPath($this->_innerPath . '/');

		// Update mod time
		$entry->setLastModified($datetime);

		$zip->commit();
		$zip->close();

		return $state;
	}

	/**
	 * Returns a merged name/value array of config elements.
	 *
	 * @return Array Merged name/value array of config elements.
	 */
	function getConfig() {
		$file =& $this->_manager->getFile($this->_zipPath);

		return $file->getConfig();
	}

	/**
	 * Opens a file stream by the specified mode. The default mode is rb.
	 *
	 * @param String $mode Mode to open file by, r, rb, w, wb etc.
	 * @return FileStream File stream implementation for the file system.
	 */
	function &open($mode = 'rb') {
		$ent = new Moxiecode_ZipFileStream($this, $mode);

		return $ent;
	}

	// * * Private methods

	function &_getZip() {
		$file = new Moxiecode_ZipFile($this->_zipPath);

		return $file;
	}

	function _setZipEntry(&$entry) {
		$this->_zipEntry = $entry;
	}

	function _getZipEntry() {
		if (!$this->_zipEntry) {
			$zip =& $this->_getZip();

			$zip->open();

			// Check for file
			$this->_zipEntry =& $zip->getEntryByPath($this->_innerPath);

			// Check for dir
			if (!$this->_zipEntry)
				$this->_zipEntry =& $zip->getEntryByPath($this->_innerPath . '/');

			$zip->close();
		}

		return $this->_zipEntry;
	}
}

class _ZipFileCopyDirTreeHandler extends Moxiecode_FileTreeHandler {
	var $_fromFile;
	var $_destFile;
	var $_manager;
	var $_handle_as_add_event;

	function _LocalCopyDirTreeHandler(&$manager, $from_file, $dest_file, $handle_as_add_event) {
		$this->_manager = $manager;
		$this->_fromFile = $from_file;
		$this->_destFile = $dest_file;
		$this->_handle_as_add_event = $handle_as_add_event;
	}

	/**
	 * Handles a file instance while looping an tree of directories.
	 * 
	 * @param File $file File object reference
	 * @param int $level Current level of tree parse
	 * @return int State of what to do next can be CONTINUE, ABORT or ABORTFOLDER.
	 */
	function handle($file, $level) {
		$toPath = $this->_destFile->getAbsolutePath();
		$toPath .= substr($file->getAbsolutePath(), strlen($this->_fromFile->getAbsolutePath()));
		$toFile = $this->_manager->getFile($toPath);

		// Do action
		if ($file->isDirectory())
			$toFile->mkdir();
		else
			$file->copyTo($toFile);

		return $this->CONTINUE;
	}
}

class Moxiecode_ZipFileStream extends Moxiecode_FileStream {
	var $_file;
	var $_mode;
	var $_pos;
	var $_buff;

	function Moxiecode_ZipFileStream(&$file, $mode) {
		$this->_file =& $file;
		$this->_mode = $mode;
		$this->_buff = "";
		$this->_pos = 0;
	}

	function skip($bytes) {
		$data = read($bytes);

		return strlen($data);
	}

	function read($len = 1024) {
		// End of stream
		if ($this->_pos > 0)
			return null;

		$this->_pos++;

		// Open zip and extract the requested data
		$zip =& $this->_file->_getZip();
		$zip->open();
		$entry =& $zip->getEntryByPath($this->_file->_innerPath);
		$data = $entry->getData();
		$zip->close();

		return $data;
	}

	function readToEnd() {
		$data = "";

		while (($chunk = $this->read(1024)) != null)
			$data .= $chunk;

		return $data;
	}

	function write($buff, $len = -1) {
		if ($len == -1)
			$len = strlen($buff);
		else
			$buff = substr($buff, 0, $len);

		$this->_pos += $len;
		$this->_buff .= $buff;
	}

	function close() {
		if ($this->_mode == 'w' || $this->_mode == 'wb' || $this->_mode == 'a' || $this->_mode == 'ab') {
			// Open zip and write the requested data
			$zip =& $this->_file->_getZip();
			$zip->open();
			$entry =& $zip->getEntryByPath($this->_file->_innerPath);

			// Overwrite existing file or create new
			if (!$entry)
				$zip->addData($this->_file->_innerPath, $this->_buff);
			else {
				if ($this->_mode == 'a' || $this->_mode == 'ab')
					$entry->setData($entry->getData() . $this->_buff);
				else
					$entry->setData($this->_buff);
			}

			$this->_buff = null;
			$zip->commit();
			$zip->close();
		}
	}
}

/*
	// Copy from local to other filesystem
	$to->importFile($from->getAbsolutePath());

	// Move from local to other filesystem
	$to->importFile($from->getAbsolutePath());
	$from->delete();

	// Copy from other to local filesystem
	$from->exportFile($to->getAbsolutePath());

	// Move from local to other filesystem
	$from->exportFile($to->getAbsolutePath());
	$from->delete();

	// Stream contents from two filesystems
	$in =& $from->open('rb');
	$out =& $to->open('wb');

	while ($buff = $in->read(8192))
		$out->write($buff);

	$out->close();
*/

?>