<?php
/**
 * $Id: FileManagerPlugin.php 751 2009-10-20 12:05:36Z spocke $
 *
 * @package MCFileManager
 * @author Moxiecode
 * @copyright Copyright © 2007, Moxiecode Systems AB, All rights reserved.
 */

require_once(MCMANAGER_ABSPATH . "FileManager/FileSystems/ZipFileImpl.php");

/**
 * This plugin class contans the core logic of the MCFileManager application.
 *
 * @package MCFileManager
 */
class Moxiecode_FileManagerPlugin extends Moxiecode_ManagerPlugin {
	/**#@+
	 * @access public
	 */

	/**
	 * Constructs a new MCFileManager instance.
	 */
	function Moxiecode_FileManagerPlugin() {
	}

	function onPreInit(&$man, $prefix) {
		global $mcFileManagerConfig;

		if ($prefix == "fm") {
			$man->setConfig($mcFileManagerConfig, false);
			$man->setLangPackPath("fm");
			return false;
		}

		return true;
	}

	/**
	 * Register file system.
	 */
	function onInit(&$man) {
		// Register file systems
		$man->registerFileSystem('zip', 'Moxiecode_ZipFileImpl');

		return true;
	}

	/**
	 * Gets executed when a RPC command is to be executed.
	 *
	 * @param MCManager $man MCManager reference that the plugin is assigned to.
	 * @param string $cmd RPC Command to be executed.
	 * @param object $input RPC input object data.
	 * @return object Result data from RPC call or null if it should be passed to the next handler in chain.
	 */
	function onRPC(&$man, $cmd, $input) {
		switch ($cmd) {
			case "copyFiles":
				return $this->_copyFiles($man, $input);

			case "moveFiles":
				return $this->_moveFiles($man, $input);

			case "createDocs":
				return $this->_createDocs($man, $input);

			case "createZip":
				return $this->_createZip($man, $input);

			case "loadContent":
				return $this->_loadContent($man, $input);

			case "saveContent":
				return $this->_saveContent($man, $input);
		}

		return null;
	}

	/**
	 * Gets called when custom data is to be added for a file custom data can for example be
	 * plugin specific name value items that should get added into a file listning.
	 *
	 * @param MCManager $man MCManager reference that the plugin is assigned to.
	 * @param BaseFile $file File reference to add custom info/data to.
	 * @param string $type Where is the info needed for example list or info.
	 * @param Array $custom Name/Value array to add custom items to.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onCustomInfo(&$man, &$file, $type, &$input) {
		switch ($type) {
			case "list":
				$input['previewable'] = $file->isFile() && $man->verifyFile($file, "preview") >= 0;
				$input['editable'] = $file->isFile() && $man->verifyFile($file, "edit") >= 0;
				break;
		}

		return true; // Pass to next
	}

	// * * * * * * * * Private methods

	function _createZip(&$man, &$input) {
		$result = new Moxiecode_ResultSet("status,fromfile,tofile,message");
		$config = $man->getConfig();

		if (!$man->isToolEnabled("zip", $config)) {
			trigger_error("{#error.no_access}", FATAL);
			die();
		}

		$zipFile = $man->getFile($man->decryptPath($input["topath"]), $input["toname"] . ".zip");
		if ($zipFile->exists()) {
			trigger_error("{#error.tofile_exists}", FATAL);
			die();
		}

		$toZip = $man->getFile("zip://" . $man->decryptPath($input["topath"]), $input["toname"] . ".zip/");

		for ($i=0; isset($input["frompath" . $i]); $i++) {
			$fromFile =& $man->getFile($man->decryptPath($input["frompath" . $i]));
			$toFile = $man->getFile("zip://" . $man->decryptPath($input["topath"]) . '/' . $input["toname"] . ".zip", $fromFile->getName());
			$zipFile = $man->getFile($man->decryptPath($input["topath"]) . '/' . $input["toname"] . ".zip");
			$config = $fromFile->getConfig();

			if (checkBool($config['general.demo'])) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.demo}");
				continue;
			}

			// Check write access
			if (!checkBool($config["filesystem.writable"])) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.no_write_access}");
				continue;
			}

			if ($man->verifyFile($fromFile, "createzip") < 0) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), $man->getInvalidFileMsg());
				continue;
			}

			if ($man->verifyFile($zipFile, "createzip") < 0) {
				$result->add("FAILED", $man->encryptPath($zipFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), $man->getInvalidFileMsg());
				continue;
			}

			if (!$fromFile->exists()) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.no_from_file}");
				continue;
			}

			if ($man->verifyFile($fromFile, "zip") < 0) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), $man->getInvalidFileMsg());
				continue;
			}

			if ($fromFile->copyTo($toFile))
				$result->add("OK", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#message.zip_success}");
			else
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.createzip_failed}");
		}

		return $result->toArray();
	}

	function _copyFiles(&$man, &$input) {
		$result = new Moxiecode_ResultSet("status,fromfile,tofile,message");
		$config = $man->getConfig();

		if (!$man->isToolEnabled("copy", $config)) {
			trigger_error("{#error.no_access}", FATAL);
			die();
		}

		for ($i=0; isset($input["frompath" . $i]); $i++) {
			$fromFile =& $man->getFile($input["frompath" . $i]);
			$fromType = $fromFile->isFile() ? MC_IS_FILE : MC_IS_DIRECTORY;

			if (isset($input["toname" . $i])) {
				$toFile =& $man->getFile($fromFile->getParent(), $input["toname" . $i], $fromType);
			} else {
				if (isset($input["topath" . $i]))
					$toFile =& $man->getFile($input["topath" . $i], "", $fromType);
				else
					$toFile =& $man->getFile($input["topath"], $fromFile->getName(), $fromType);
			}

			if (!$fromFile->exists()) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.no_from_file}");
				continue;
			}

			if ($toFile->exists()) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.tofile_exists}");
				continue;
			}

			$toConfig = $toFile->getConfig();

			if (checkBool($toConfig['general.demo'])) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.demo}");
				continue;
			}

			if ($man->verifyFile($toFile, "copy") < 0) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), $man->getInvalidFileMsg());
				continue;
			}

			if (!$toFile->canWrite()) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.no_write_access}");
				continue;
			}

			if (!checkBool($toConfig["filesystem.writable"])) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.no_write_access}");
				continue;
			}

			// Check if file can be zipped
			if (getClassName($toFile) == 'moxiecode_zipfileimpl') {
				if ($man->verifyFile($fromFile, "zip") < 0) {
					$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), $man->getInvalidFileMsg());
					continue;
				}
			}

			// Check if file can be unzipped
			if (getClassName($fromFile) == 'moxiecode_zipfileimpl') {
				if ($man->verifyFile($toFile, "unzip") < 0) {
					$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), $man->getInvalidFileMsg());
					continue;
				}
			}

			if ($fromFile->copyTo($toFile))
				$result->add("OK", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#message.copy_success}");
			else
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.copy_failed}");
		}

		return $result->toArray();
	}

	function _moveFiles(&$man, &$input) {
		$result = new Moxiecode_ResultSet("status,fromfile,tofile,message");
		$config = $man->getConfig();

		if (!$man->isToolEnabled("rename", $config) && !$man->isToolEnabled("cut", $config)) {
			trigger_error("{#error.no_access}", FATAL);
			die();
		}

		for ($i=0; isset($input["frompath" . $i]); $i++) {
			$fromFile =& $man->getFile($input["frompath" . $i]);
			$fromType = $fromFile->isFile() ? MC_IS_FILE : MC_IS_DIRECTORY;

			if (isset($input["toname" . $i])) {
				$toFile =& $man->getFile($fromFile->getParent(), $input["toname" . $i], $fromType);
			} else {
				if (isset($input["topath" . $i]))
					$toFile =& $man->getFile($input["topath" . $i], "", $fromType);
				else
					$toFile =& $man->getFile($input["topath"], $fromFile->getName(), $fromType);
			}

			// User tried to change extension
			if ($fromFile->isFile() && getFileExt($fromFile->getName()) != getFileExt($toFile->getName())) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.move_failed}");
				continue;
			}

			if (!$fromFile->exists()) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.no_from_file}");
				continue;
			}

			if ($toFile->exists()) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.tofile_exists}");
				continue;
			}

			$toConfig = $toFile->getConfig();

			if (checkBool($toConfig['general.demo'])) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.demo}");
				continue;
			}

			if ($man->verifyFile($toFile, "rename") < 0) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), $man->getInvalidFileMsg());
				continue;
			}

			if (!$toFile->canWrite()) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.no_write_access}");
				continue;
			}

			if (!checkBool($toConfig["filesystem.writable"])) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.no_write_access}");
				continue;
			}

			// Check if file can be zipped
			if (getClassName($toFile) == 'moxiecode_zipfileimpl') {
				if ($man->verifyFile($fromFile, "zip") < 0) {
					$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), $man->getInvalidFileMsg());
					continue;
				}
			}

			// Check if file can be unzipped
			if (getClassName($fromFile) == 'moxiecode_zipfileimpl') {
				if ($man->verifyFile($toFile, "unzip") < 0) {
					$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), $man->getInvalidFileMsg());
					continue;
				}
			}

			if ($fromFile->renameTo($toFile))
				$result->add("OK", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#message.move_success}");
			else
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.move_failed}");
		}

		return $result->toArray();
	}

	function _createDocs(&$man, &$input) {
		$result = new Moxiecode_ResultSet("status,fromfile,tofile,message");
		$config = $man->getConfig();

		if (!$man->isToolEnabled("createdoc", $config)) {
			trigger_error("{#error.no_access}", FATAL);
			die();
		}

		for ($i=0; isset($input["frompath" . $i]) && isset($input["toname" . $i]); $i++) {
			$fromFile =& $man->getFile($input["frompath" . $i]);
			$ext = getFileExt($fromFile->getName());
			$toFile =& $man->getFile($input["topath" . $i], $input["toname" . $i] . '.' . $ext);
			$toConfig = $toFile->getConfig();

			if (checkBool($toConfig['general.demo'])) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.demo}");
				continue;
			}

			if ($man->verifyFile($toFile, "createdoc") < 0) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), $man->getInvalidFileMsg());
				continue;
			}

			if (!$toFile->canWrite()) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.no_write_access}");
				continue;
			}

			if (!checkBool($toConfig["filesystem.writable"])) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.no_write_access}");
				continue;
			}

			if (!$fromFile->exists()) {
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.template_missing}");
				continue;
			}

			if ($fromFile->copyTo($toFile)) {
				// Replace title
				$fields = $input["fields"];

				// Replace all fields
				if ($fields) {
					// Read all data
					$stream = $toFile->open('r');
					$fileData = $stream->readToEnd();
					$stream->close();

					// Replace fields
					foreach ($fields as $name => $value)
						$fileData = str_replace('${' . $name . '}', htmlentities($value), $fileData);

					// Write file data
					$stream = $toFile->open('w');
					$stream->write($fileData);
					$stream->close();
				}

				$result->add("OK", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#message.createdoc_success}");
			} else
				$result->add("FAILED", $man->encryptPath($fromFile->getAbsolutePath()), $man->encryptPath($toFile->getAbsolutePath()), "{#error.createdoc_failed}");
		}

		return $result->toArray();
	}

	function _loadContent(&$man, &$input) {
		$file = $man->getFile($input["path"]);
		$config = $file->getConfig();

		if (!$man->isToolEnabled("edit", $config)) {
			trigger_error("{#error.no_access}", FATAL);
			die();
		}

		if (!checkBool($config["filesystem.writable"])) {
			trigger_error("{#error.no_write_access}", FATAL); 
			die();
		}

		if ($man->verifyFile($file, "edit") < 0) {
			trigger_error($man->getInvalidFileMsg(), FATAL); 
			die();
		}

		if (!$file->canWrite()) {
			trigger_error("{#error.no_write_access}", FATAL); 
			die();
		}

		$reader = $file->open('r');
		if ($reader) {
			$content = $reader->readToEnd();
			$reader->close();
		} else {
			trigger_error("{#error.no_read_access}", FATAL); 
			die();
		}

		return array("content" => $content);
	}

	function _saveContent(&$man, &$input) {
		$file = $man->getFile($input["path"]);
		$config = $file->getConfig();

		if (!$man->isToolEnabled("edit", $config)) {
			trigger_error("{#error.no_access}", FATAL);
			die();
		}

		if (checkBool($config['general.demo'])) {
			trigger_error("{#error.demo}", FATAL); 
			die();
		}

		if (!checkBool($config["filesystem.writable"])) {
			trigger_error("{#error.no_write_access}", FATAL); 
			die();
		}

		if ($man->verifyFile($file, "edit") < 0) {
			trigger_error($man->getInvalidFileMsg(), FATAL); 
			die();
		}

		if (!$file->canWrite()) {
			trigger_error("{#error.no_write_access}", FATAL); 
			die();
		}

		$writer = $file->open('w');
		if ($writer) {
			$writer->write($input["content"]);
			$writer->close();
		} else {
			trigger_error("{#error.no_write_access}", FATAL); 
			die();
		}

		return array("status" => "OK");
	}

	/**#@-*/
}

// Add plugin to MCManager
$man->registerPlugin("filemanager", new Moxiecode_FileManagerPlugin(), "fm");

?>