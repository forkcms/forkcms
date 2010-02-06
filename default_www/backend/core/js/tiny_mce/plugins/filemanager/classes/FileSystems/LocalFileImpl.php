<?php
/**
 * $Id: LocalFileImpl.php 547 2008-10-31 14:05:36Z spocke $
 *
 * @package MCFileManager.filesystems
 * @author Moxiecode
 * @copyright Copyright © 2005, Moxiecode Systems AB, All rights reserved.
 */

/**
 * This is the local file system implementation of BaseFile.
 *
 * @package MCFileManager.filesystems
 */
class Moxiecode_LocalFileImpl extends Moxiecode_BaseFile {
	// Private fields
	var $_absPath = "";
	var $_manager;
	var $_config;
	var $_type;
	var $_events = true;
	var $_statCache = array();

	/**
	 * Creates a new absolute file.
	 *
	 * @param MCManager $manager MCManager reference.
	 * @param String $absolute_path Absolute path to local file.
	 * @param String $child_name Name of child file (Optional).
	 * @param String $type Optional file type.
	 */
	function Moxiecode_LocalFileImpl(&$manager, $absolute_path, $child_name = "", $type = MC_IS_FILE) {
		$this->_manager =& $manager;
		$this->_type = $type;

		if ($child_name != "")
			 $this->_absPath = $this->_manager->removeTrailingSlash($absolute_path) . "/" . $child_name;
		else
			$this->_absPath = $absolute_path;

		// Force it absolute
		if (strpos($this->_absPath, '..') !== false)
			$this->_absPath = $this->_manager->toUnixPath(realpath($this->_absPath));
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
		$pathAr = explode("/", $this->_absPath);

		array_pop($pathAr);
		$path = implode("/", $pathAr);

		// TODO: Check this
		return ($path == "") ? null : $path;
	}

	/**
	 * Returns the parent files File instance.
	 *
	 * @return File parent files File instance or false if there is no more parents.
	 */
	function &getParentFile() {
		$file = new Moxiecode_LocalFileImpl($this->_manager, $this->getParent());
		return $file;
	}

	/**
	 * Returns the file name of a file.
	 *
	 * @return string File name of file.
	 */
	function getName() {
		return basename($this->_manager->toOSPath($this->_absPath));
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
	 * Imports a local file to the file system, for example when users upload files.
	 *											   
	 * @param String $local_absolute_path Absolute path to local file to import.
	 */
	function importFile($local_absolute_path = "") {
		$this->_clearCache();

		// Add action
		if ($this->_events) {
			if ($this->isFile()) {
				$this->_manager->dispatchEvent("onBeforeFileAction", array(ADD_ACTION, &$this));
				$this->_manager->dispatchEvent("onFileAction", array(ADD_ACTION, &$this));
			} else {
				$this->_manager->dispatchEvent("onBeforeFileAction", array(MKDIR_ACTION, &$this));
				$this->_manager->dispatchEvent("onFileAction", array(MKDIR_ACTION, &$this));
			}
		}

		// Chmod the file
		if (($mask = $this->_getConfigValue("filesystem.local.file_mask", false)) !== false)
			@chmod($this->_manager->toOSPath($this->_absPath), intval($mask, 8));

		// Chown the file
		if (($owner = $this->_getConfigValue("filesystem.local.file_owner", false)) !== false)
			$this->_chown($this->_manager->toOSPath($this->_absPath), $owner);
	}

	/**
	 * Returns true if the file exists.
	 *
	 * @return boolean true if the file exists.
	 */
	function exists() {
		if (isset($this->_statCache['exists']))
			return $this->_statCache['exists'];

		// Returns false if safe mode is on and the user/group is not the same as apache
		return ($this->_statCache['exists'] = file_exists($this->_manager->toOSPath($this->_absPath)));
	}

	/**
	 * Returns true if the file is a directory.
	 *
	 * @return boolean true if the file is a directory.
	 */
	function isDirectory() {
		if (!$this->exists())
			return $this->_type == MC_IS_DIRECTORY;

		if (isset($this->_statCache['is_dir']))
			return $this->_statCache['is_dir'];

		return ($this->_statCache['is_dir'] = is_dir($this->_manager->toOSPath($this->_absPath)));
	}

	/**
	 * Returns true if the file is a file.
	 *
	 * @return boolean true if the file is a file.
	 */
	function isFile() {
		if (!$this->exists())
			return $this->_type == MC_IS_FILE;

		if (isset($this->_statCache['is_file']))
			return $this->_statCache['is_file'];

		return ($this->_statCache['is_file'] = is_file($this->_manager->toOSPath($this->_absPath)));
	}

	/**
	 * Returns last modification date in ms as an long.
	 *
	 * @return long last modification date in ms as an long.
	 */
	function getLastModified() {
		if (!isset($this->_statCache['stat']))
			$this->_statCache['stat'] = @stat($this->_manager->toOSPath($this->_absPath));

		return $this->_statCache['stat'][9];
	}

	/**
	 * Returns creation date in ms as an long.
	 *
	 * @return long creation date in ms as an long.
	 */
	function getCreationDate() {
		if (!isset($this->_statCache['stat']))
			$this->_statCache['stat'] = @stat($this->_manager->toOSPath($this->_absPath));

		return $this->_statCache['stat'][10];
	}

	/**
	 * Returns true if the files is readable.
	 *
	 * @return boolean true if the files is readable.
	 */
	function canRead() {
		if (isset($this->_statCache['is_readable']))
			return $this->_statCache['is_readable'];

		return ($this->_statCache['is_readable'] = is_readable($this->_manager->toOSPath($this->_absPath)));
	}

	/**
	 * Returns true if the files is writable.
	 *
	 * @return boolean true if the files is writable.
	 */
	function canWrite() {
		// Check parent
		if (!$this->exists()) {
			$file =& $this->getParentFile();

			if ($this->getAbsolutePath() == $file->getAbsolutePath())
				return false;

			return $file->canWrite();
		}

		if (isset($this->_statCache['is_writeable']))
			return $this->_statCache['is_writeable'];

		// Is windows
		if (DIRECTORY_SEPARATOR == "\\") {
			$path = $this->_manager->toOSPath($this->_absPath);

			if (is_file($path)) {
				$fp = @fopen($path, 'ab');

				if ($fp) {
					fclose($fp);
					return true;
				}
			} else if (is_dir($path)) {
				$tmpnam = time() . md5(uniqid('iswritable'));

				if (@touch($path . '\\' . $tmpnam)) {
					unlink($path . '\\' . $tmpnam);
					return true;
				}
			}

			return false;
		}

		// Other OS:es
		return ($this->_statCache['is_writeable'] = is_writeable($this->_manager->toOSPath($this->_absPath)));
	}

	/**
	 * Returns file size as an long.
	 *
	 * @return long file size as an long.
	 */
	function getLength() {
		if (!isset($this->_statCache['stat']))
			$this->_statCache['stat'] = @stat($this->_manager->toOSPath($this->_absPath));

		return $this->_statCache['stat'][7];
	}

	/**
	 * Copies this file to the specified file instance.
	 *
	 * @param File $dest File to copy to.
	 * @return boolean true - success, false - failure
	 */
	function copyTo(&$dest) {
		$this->_clearCache();

		if ($dest->exists())
			return false;

		// Copy in to your self?
		if (strpos($dest->getAbsolutePath(), $this->getAbsolutePath()) === 0)
			return false;

		// Different file system then import
		if (getClassName($dest) != 'moxiecode_localfileimpl')
			return $dest->importFile($this->getAbsolutePath());

		$dest->_clearCache();

		if ($this->isDirectory()) {
			$handle_as_add_event = true;
			$treeHandler = new _LocalCopyDirTreeHandler($this->_manager, $this, $dest, $handle_as_add_event);

			$this->listTree($treeHandler);

			return true;
		} else {
			if ($this->_events)
				$this->_manager->dispatchEvent("onBeforeFileAction", array(COPY_ACTION, &$this, &$dest));

			$status = $this->_absPath == $dest->_absPath || copy($this->_manager->toOSPath($this->_absPath), $this->_manager->toOSPath($dest->_absPath));

			// Chmod the file
			if (($mask = $dest->_getConfigValue("filesystem.local.file_mask", false)) !== false)
				@chmod($this->_manager->toOSPath($dest->_absPath), intval($mask, 8));

			// Chown the file
			if (($owner = $dest->_getConfigValue("filesystem.local.file_owner", false)) !== false)
				$this->_chown($this->_manager->toOSPath($dest->_absPath), $owner);

			if ($status && $this->_events)
				$this->_manager->dispatchEvent("onFileAction", array(COPY_ACTION, &$this, &$dest));

			return $status;
		}
	}

	/**
	 * Deletes the file.
	 *
	 * @param boolean $deep If this option is enabled files will be deleted recurive.
	 * @return boolean true - success, false - failure
	 */
	function delete($deep = false) {
		if (!$this->exists())
			return false;

		$this->_clearCache();

		if (is_dir($this->_manager->toOSPath($this->_absPath))) {
			if ($this->_events)
				$this->_manager->dispatchEvent("onBeforeFileAction", array(RMDIR_ACTION, &$this));

			if ($deep) {
				// Get all the files
				$treeHandler = new _LocalDeleteDirTreeHandler($this->_manager, $this);
				$this->listTree($treeHandler);

				// Delete the files
				$files = array_reverse($treeHandler->getFiles());
				foreach ($files as $file)
					$file->delete();

				// Hmm, should be better
				return true;
			}

			if (($status = rmdir($this->_manager->toOSPath($this->_absPath))) && ($this->_events))
				$this->_manager->dispatchEvent("onFileAction", array(RMDIR_ACTION, &$this));
		} else {
			if ($this->_events)
				$this->_manager->dispatchEvent("onBeforeFileAction", array(DELETE_ACTION, &$this));

			if (($status = unlink($this->_manager->toOSPath($this->_absPath))) && ($this->_events))
				$this->_manager->dispatchEvent("onFileAction", array(DELETE_ACTION, &$this));
		}

		return $status;
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
	 * Returns an array of BaseFile instances based on the specified filter instance.
	 *
	 * @param FileFilter &$filter FileFilter instance to filter files by.
	 * @return Array array of File instances based on the specified filter instance.
	 */
	function &listFilesFiltered(&$filter) {
	 	$dir = $this->_absPath;
	 	$files = array();
	 	$dirs = array();
		$fileArray = array();
		$dirArray = array();

		if ($fHnd = opendir($this->_manager->toOSPath($dir))) {
			// Is there a trailing slash on the dir? Get rid of it
			//if ($dir[sizeof($dir)] == "/")
			//	$dir = substr($dir, 0, sizeof($dir) -1);

			while (false !== ($file = readdir($fHnd))) {
				if ($file == "." || $file == "..")
					continue;

				// Returns false if safe mode is on and the user/group is not the same as apache
				$path = $dir . "/" . $file;
				if (file_exists($path)) {
					// We are at root!
					if ($dir == "/")
						$dir = "";

					if (is_file($path))
						$fileArray[] = $file;
					else
						$dirArray[] = $file;
				}
			}

			// Close handle
			closedir($fHnd);

			sort($dirArray);
			sort($fileArray);

			// Add dirs
			foreach ($dirArray as $adir) {
				$file = new Moxiecode_LocalFileImpl($this->_manager, $dir . "/" . $adir);

				if ($filter->accept($file) < 0)
					continue;

				array_push($files, $file);
			}

			// Add files
			foreach ($fileArray as $afile) {
				$file = new Moxiecode_LocalFileImpl($this->_manager, $dir . "/" . $afile);

				if ($filter->accept($file) < 0)
					continue;

				array_push($files, $file);
			}
		}

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

	/**
	 * Creates a new directory.
	 *
	 * @return boolean true- success, false - failure
	 */
	function mkdir() {
		$this->_clearCache();

		if ($this->_events)
			$this->_manager->dispatchEvent("onBeforeFileAction", array(MKDIR_ACTION, &$this));

		$tpl = $this->_getConfigValue("filesystem.local.directory_template", false);

		if ($tpl)
			$status = copy($this->_manager->toAbsPath($tpl), $this->_manager->toOSPath($this->_absPath));
		else
			$status = mkdir($this->_manager->toOSPath($this->_absPath));

		// Chmod the dir
		if (($mask = $this->_getConfigValue("filesystem.local.directory_mask", false)) !== false)
			@chmod($this->_manager->toOSPath($this->_absPath), intval($mask, 8));

		// Chown the dir
		if (($owner = $this->_getConfigValue("filesystem.local.directory_owner", false)) !== false)
			$this->_chown($this->_manager->toOSPath($this->_absPath), $owner);

		if ($status && $this->_events)
			$this->_manager->dispatchEvent("onFileAction", array(MKDIR_ACTION, &$this));

		return $status;
	}

	/**
	 * Renames/Moves this file to the specified file instance.
	 *
	 * @param File $dest File to rename/move to.
	 * @return boolean true- success, false - failure
	 */
	function renameTo(&$dest) {
		$this->_clearCache();

		// Already exists
		if ($dest->exists())
			return false;

		if ($this->_events)
			$this->_manager->dispatchEvent("onBeforeFileAction", array(RENAME_ACTION, &$this, &$dest));

		if ($this->_absPath != $dest->_absPath)
			$status = rename($this->_manager->toOSPath($this->_absPath), $this->_manager->toOSPath($dest->_absPath));

		if ($status && $this->_events)
			$this->_manager->dispatchEvent("onFileAction", array(RENAME_ACTION, &$this, &$dest));

		$isFile = is_file($this->_manager->toOSPath($this->_absPath));

		// Chmod the file/directory
		if (($mask = $dest->_getConfigValue("filesystem.local." . ($isFile ? "file" : "directory") . "_mask", false)) !== false)
			@chmod($this->_manager->toOSPath($dest->_absPath), intval($mask, 8));

		// Chown the file/directory
		if (($owner = $dest->_getConfigValue("filesystem.local." . ($isFile ? "file" : "directory") . "_owner", false)) !== false)
			$this->_chown($this->_manager->toOSPath($dest->_absPath), $owner);

		return $status;
	}

	/*
	 * Sets the last-modified time of the file or directory.
	 *
	 * @param String $datetime The new date/time to set the file, in timestamp format
	 * @return boolean true- success, false - failure
	 */
	function setLastModified($datetime) {
		$this->_clearCache();
		return touch($this->_manager->toOSPath($this->_absPath), $datetime);
	}

	/**
	 * Opens a file stream by the specified mode. The default mode is rb.
	 *
	 * @param String $mode Mode to open file by, r, rb, w, wb etc.
	 * @return FileStream File stream implementation for the file system.
	 */
	function &open($mode = 'rb') {
		$this->_clearCache();
		$stream = new Moxiecode_LocalFileStream($this, $mode);

		return $stream;
	}

	// * * Private methods

	function _clearCache() {
		$this->_statCache = array();
	}

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

	/**
	 * Returns a merged name/value array of config elements.
	 *
	 * @return Array Merged name/value array of config elements.
	 */
	function getConfig() {
		$globalConf = $this->_manager->getConfig();

		// Not cached config
		if (!$this->_config) {
			$localConfig = array();

			$this->_config = $globalConf;

			// Get files up the tree
			$accessFiles = array();
			$file =& $this;

			if ($file->isFile())
				$file =& $file->getParentFile();

			while ($file->exists() && $file->getAbsolutePath() != "/") {
				$accessFile = new Moxiecode_LocalFileImpl($this->_manager, $file->getAbsolutePath(), $globalConf["filesystem.local.access_file_name"]);
				if ($accessFile->exists())
					$accessFiles[] = $accessFile;

				// Is not a valid path break loop
				if (!$this->_manager->verifyPath($file->getParent()))
					break;

				$file =& $file->getParentFile();
			}

			// Parse and merge
			$allowoverrideKeys = array();
			foreach ($this->_config as $key => $value) {
				$keyChunks = explode('.', $key);

				if ($keyChunks[count($keyChunks)-1] == "allow_override") {
					foreach (explode(',', $value) as $keySuffix) {
						$keyChunks[count($keyChunks)-1] = $keySuffix;
						$allowoverrideKeys[] = implode('.', $keyChunks);
					}
				}
			}

			foreach (array_reverse($accessFiles) as $accessFile) {
				$config = array();

				// Parse ini file
				if (($fp = fopen($this->_manager->toOSPath($accessFile->getAbsolutePath()), "r"))) {
					while (!feof($fp)) {
						$line = trim(fgets($fp));

						// Skip comments
						if (substr($line, 0, 1) == "#")
							continue;

						// Split rows
						if (($pos = strpos($line, "=")) !== FALSE)
							$config[substr($line, 0, $pos)] = substr($line, $pos+1);
					}

					fclose($fp);
				}

				// Handle local config values
				$curDir = $this->isFile() ? $this->getParent() : $this->getAbsolutePath();
				if ($accessFile->getParent() == $curDir) {
					foreach ($config as $key => $value) {
						if (substr($key, 0, 1) == '_')
							$localConfig[substr($key, 1)] = $value;
					}
				}

				// Parse allow keys and deny keys
				$denyOverrideKeys = array();
				foreach ($config as $key => $value) {
					$keyChunks = explode('.', $key);
					$lastChunk = $keyChunks[count($keyChunks)-1];

					if ($lastChunk == "allow_override" || $lastChunk == "deny_override") {
						foreach (explode(',', $value) as $keySuffix) {
							$keyChunks[count($keyChunks)-1] = $keySuffix;
							$allowDenyKey = implode('.', $keyChunks);

							if (in_array($allowDenyKey, $allowoverrideKeys)) {
								if ($lastChunk == "allow_override")
									$allowoverrideKeys[] = $allowDenyKey;
								else
									$denyOverrideKeys[] = $allowDenyKey;
							}
						}
					}
				}

				// Remove the denied keys from the allow list
/*				foreach ($denyOverrideKeys as $denyKey) {
					for ($i=0; $i<count($allowoverrideKeys); $i++) {
						if ($denyKey == $allowoverrideKeys[$i])
							unset($allowoverrideKeys[$i]);
					}
				}*/

				// Add all overriden values
				foreach ($config as $key => $value) {
					$validAllKey = false;

					foreach ($allowoverrideKeys as $allowkey) {
						if (strpos($allowkey, "*") > 0) {
							$allowkey = str_replace("*", "", $allowkey);
							// echo $allowkey . "," . $key . strpos($allowkey, $key) . "<br>";
							if (strpos($key, $allowkey) === 0) {
								$validAllKey = true;
								break;
							}
						}
					}

					if ((in_array($key, $allowoverrideKeys) || $validAllKey) && !in_array($key, $denyOverrideKeys)) {
						if (strpos($value, '${') !== false)
							$value = str_replace('${configpath}', $accessFile->getParent(), $value);

						$this->_config[$key] = $value;
					}
				}
			}

			// Variable substitute the values
			foreach ($this->_config as $key => $value) {
				if (!is_array($value) && strpos($value, '${') !== false) {
					if ($this->isFile())
						$path = $this->getAbsolutePath();
					else
						$path = $this->getParent();

					$value = str_replace('${path}', $path, $value);
					$value = str_replace('${rootpath}', $this->_manager->toUnixPath($this->_manager->_rootPaths[0]), $value);

					// Handle multiple rootpaths
					for ($i=0; $i<count($this->_manager->_rootPaths); $i++)
						$value = str_replace('${rootpath' . $i . '}', $this->_manager->toUnixPath($this->_manager->_rootPaths[$i]), $value);

					$this->_config[$key] = $value;
				}
			}

			// Force local config
			foreach ($localConfig as $key => $value) {
				//$this->_config[$key] = $value;

				$validAllKey = false;

				foreach ($allowoverrideKeys as $allowkey) {
					if (strpos($allowkey, "*") > 0) {
						$allowkey = str_replace("*", "", $allowkey);
						// echo $allowkey . "," . $key . strpos($allowkey, $key) . "<br>";
						if (strpos($key, $allowkey) === 0) {
							$validAllKey = true;
							break;
						}
					}
				}

				if ((in_array($key, $allowoverrideKeys) || $validAllKey) && !in_array($key, $denyOverrideKeys)) {
					if (strpos($value, '${') !== false)
						$value = str_replace('${configpath}', $accessFile->getParent(), $value);

					$this->_config[$key] = $value;
				}
			}

/*			foreach ($this->_config as $key => $value) {
				if (in_array($key, $allowoverrideKeys)) {
					// Seems to be a variable
					if (strpos($value, '${') !== false) {
						$matches = array();
						preg_match_all('/\${(.*)}/i', $value, $matches);
						var_dump($matches);
						foreach ($matches as $match)
							$this->_config[$key] = str_replace('${' . $match . '}', $this->_config[$match], $this->_config[$key]);
					}
				}
			}*/
		}

		return $this->_config;
	}

	/**
	 * Returns a config value by the specified key.
	 *
	 * @param String key Key to return value by.
	 * @param String default_value Optional default value.
	 * @return String Value returned from config.
	 */
	function _getConfigValue($key, $default_value = "") {
		$config = $this->getConfig();
		
		if (isset($config[$key]))
			return $config[$key] ? $config[$key] : $default_value;
		else
			return $default_value;
	}

	function _chown($path, $owner) {
		if ($owner == "")
			return;

		$owner = explode(":", $owner);

		// Only user
		if (count($owner) == 1)
			array_push($owner, "");

		// Hmm
		if (count($owner) != 2)
			return;

		// Set user
		if ($owner[0] != "")
			@chown($path, $owner[0]);

		// Set group
		if ($owner[1] != "")
			@chgrp($path, $owner[1]);
	}

	function _cmpByName($file1, $file2) {
		$al = strtolower($file1->getName());
		$bl = strtolower($file2->getName());

		if ($al == $bl)
			return 0;

		return $al > $bl ? 1 : -1;
	}
}

class _LocalCopyDirTreeHandler extends Moxiecode_FileTreeHandler {
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

		//debug($file->getAbsolutePath() . "->" . $toPath);

		// Do action
		if ($file->isDirectory())
			$toFile->mkdir();
		else
			$file->copyTo($toFile);

		return $this->CONTINUE;
	}
}

class _LocalDeleteDirTreeHandler extends Moxiecode_FileTreeHandler {
	var $_manager;
	var $_dir;
	var $_files;

	function _LocalDeleteDirTreeHandler(&$manager, $dir) {
		$this->_manager = $manager;
		$this->_dir = $dir;
		$this->_files = array();
	}

	/**
	 * Returns all the files and dirs.
	 *
	 * @return Array All the files and dirs.
	 */
	function getFiles() {
		return $this->_files;
	}

	/**
	 * Handles a file instance while looping an tree of directories.
	 * 
	 * @param File $file File object reference
	 * @param int $level Current level of tree parse
	 * @return int State of what to do next can be CONTINUE, ABORT or ABORTFOLDER.
	 */
	function handle($file, $level) {
		$this->_files[] = $file;

		//$file->delete();
		return $this->CONTINUE;
	}
}

class Moxiecode_LocalFileStream extends Moxiecode_FileStream {
	var $_fp;
	var $_file;

	function Moxiecode_LocalFileStream(&$file, $mode) {
		$this->_file = $file;
		$this->_fp = fopen($file->getAbsolutePath(), $mode);
	}

	function skip($bytes) {
		$pos = ftell($this->_fp);

		fseek($this->_fp, $bytes, SEEK_CUR);

		return ftell($this->_fp) - $pos;
	}

	function read($len = 1024) {
		return fread($this->_fp, $len);
	}

	function readToEnd() {
		$data = "";

		while (($chunk = $this->read(1024)) != null)
			$data .= $chunk;

		return $data;
	}

	function write($buff, $len = -1) {
		if ($len == -1)
			return fwrite($this->_fp, $buff);

		return fwrite($this->_fp, $buff, $len);
	}

	function close() {
		$this->_file->_clearCache();
		@fclose($this->_fp);
		$this->_fp = null;
	}
}

?>