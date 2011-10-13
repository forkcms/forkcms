<?php
/**
 * $Id: ImageManagerPlugin.php 751 2009-10-20 12:05:36Z spocke $
 *
 * @package MCImageManager
 * @author Moxiecode
 * @copyright Copyright © 2007, Moxiecode Systems AB, All rights reserved.
 */

require_once(MCMANAGER_ABSPATH . "ImageManager/Utils/MCImageToolsGD.php");

/**
 * This plugin class contans the core logic of the MCImageManager application.
 *
 * @package MCImageManager
 */
class Moxiecode_ImageManagerPlugin extends Moxiecode_ManagerPlugin {
	/**#@+
	 * @access public
	 */

	/**
	 * Constructs a new imagemanager instance.
	 */
	function Moxiecode_ImageManagerPlugin() {
	}

	function onPreInit(&$man, $prefix) {
		global $mcImageManagerConfig;

		if ($prefix == "im") {
			$man->setConfig($mcImageManagerConfig, false);
			$man->setLangPackPath("im");
			return false;
		}

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
			case "getMediaInfo":
				return $this->_getMediaInfo($man, $input);

			case "resizeImage":
				$result = new Moxiecode_ResultSet("status,file,message");
				$file = $man->getFile($input["path"]);

				/*
				if ($man->verifyFile($file, "edit") < 0) {
					$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), $man->getInvalidFileMsg());
					return $result->toArray();
				}*/

				$filedata = array();
				$filedata["path"] = $man->encryptPath($file->getAbsolutePath());
				$filedata["width"] = isset($input["width"]) ? $input["width"] : 0;
				$filedata["height"] = isset($input["height"]) ? $input["height"] : 0;
				$filedata["target"] = isset($input["target"]) ? $input["target"] : "";
				$filedata["temp"] = isset($input["temp"]) ? $input["temp"] : "";

				$this->_resizeImage($man, $file, $filedata, $result);

				return $result->toArray();

			case "cropImage":
				$result = new Moxiecode_ResultSet("status,file,message");
				$file = $man->getFile($input["path"]);

				/*
				if ($man->verifyFile($file, "edit") < 0) {
					$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), $man->getInvalidFileMsg());
					return $result->toArray();
				}*/

				$filedata = array();
				$filedata["path"] = $man->encryptPath($file->getAbsolutePath());
				$filedata["width"] = $input["width"];
				$filedata["height"] = $input["height"];
				$filedata["top"] = $input["top"];
				$filedata["left"] = $input["left"];
				$filedata["target"] = isset($input["target"]) ? $input["target"] : "";
				$filedata["temp"] = isset($input["temp"]) ? $input["temp"] : "";

				$this->_cropImage($man, $file, $filedata, $result);

				return $result->toArray();

			case "rotateImage":
				$result = new Moxiecode_ResultSet("status,file,message");
				$file = $man->getFile($input["path"]);

				/*
				if ($man->verifyFile($file, "edit") < 0) {
					$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), $man->getInvalidFileMsg());
					return $result->toArray();
				}*/

				$filedata = array();
				$filedata["path"] = $file->getAbsolutePath();
				$filedata["angle"] = $input["angle"];
				$filedata["target"] = isset($input["target"]) ? $input["target"] : "";
				$filedata["temp"] = isset($input["temp"]) ? $input["temp"] : "";

				$this->_rotateImage($man, $file, $filedata, $result);

				return $result->toArray();

			case "flipImage":
				$result = new Moxiecode_ResultSet("status,file,message");
				$file = $man->getFile($input["path"]);

				/*
				if ($man->verifyFile($file, "edit") < 0) {
					$result->add("ACCESS_ERROR", $man->encryptPath($file->getAbsolutePath()), $man->getInvalidFileMsg());
					return $result->toArray();
				}*/

				$filedata = array();
				$filedata["path"] = $file->getAbsolutePath();
				$filedata["vertical"] = isset($input["vertical"]) ? $input["vertical"] : false;
				$filedata["horizontal"] = isset($input["horizontal"]) ? $input["horizontal"] : false;
				$filedata["target"] = isset($input["target"]) ? $input["target"] : "";
				$filedata["temp"] = isset($input["temp"]) ? $input["temp"] : "";

				$this->_flipImage($man, $file, $filedata, $result);

				return $result->toArray();

			case "saveImage":
				$config = $man->getConfig();
				$result = new Moxiecode_ResultSet("status,file,message");
				$file = $man->getFile($input["path"]);

				if (checkBool($config["general.demo"])) {
					$result->add("FAILED", $man->encryptPath($input["target"]), "{#error.demo}");
					$this->_cleanUp($man, $file->getParent());
					return $result->toArray();
				}

				/*if ($man->verifyFile($file, "edit") < 0) {
					$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), $man->getInvalidFileMsg());
					$this->_cleanUp($man, $file->getParent());
					return $result->toArray();
				}*/

				$filedata = array();
				$filedata["path"] = $file->getAbsolutePath();

				if (isset($input["target"]) && $input["target"] != "") {
					$targetFile = $man->getFile(utf8_encode($file->getParent()), $input["target"]);
					$filedata["target"] = utf8_encode($targetFile->getAbsolutePath());
				}

				$this->_saveImage($man, $file, $filedata, $result);
				$this->_cleanUp($man, $file->getParent());

				return $result->toArray();
		}

		return null;
	}

	/**
	 * Gets called when data is streamed to client. This method should setup
	 * HTTP headers, content type etc and simply send out the binary data to the client and the return false
	 * ones that is done.
	 *
	 * @param MCManager $man MCManager reference that the plugin is assigned to.
	 * @param string $cmd Stream command that is to be performed.
	 * @param string $input Array of input arguments.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onStream(&$man, $cmd, $input) {
		switch ($cmd) {
			case "thumb":
				return $this->_streamThumb($man, $input);
		}

		return null;
	}

	/**
	 * Gets called before a file action occurs for example before a rename or copy.
	 *
	 * @param ManagerEngine $man ManagerEngine reference that the plugin is assigned to.
	 * @param int $action File action constant for example DELETE_ACTION.
	 * @param BaseFile $file1 File object 1 for example from in a copy operation.
	 * @param BaseFile $file2 File object 2 for example to in a copy operation. Might be null in for example a delete.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onBeforeFileAction(&$man, $action, $file1, $file2) {
		if ($action == DELETE_ACTION) {
			// Delete format images
			$config = $file1->getConfig();

			if (checkBool($config['filesystem.delete_format_images'])) {
				$imageutils = new $config['thumbnail'];
				$imageutils->deleteFormatImages($file1->getAbsolutePath(), $config["upload.format"]);
				$imageutils->deleteFormatImages($file1->getAbsolutePath(), $config["edit.format"]);
			}
		}

		return true;
	}

	/**
	 * Gets called after a file action was perforem for example after a rename or copy.
	 *
	 * @param MCManager $man MCManager reference that the plugin is assigned to.
	 * @param int $action File action constant for example DELETE_ACTION.
	 * @param string $file1 File object 1 for example from in a copy operation.
	 * @param string $file2 File object 2 for example to in a copy operation. Might be null in for example a delete.
	 * @return bool true/false if the execution of the event chain should continue.
	 */
	function onFileAction(&$man, $action, $file1, $file2) {
		switch ($action) {
			case ADD_ACTION:
				$config = $file1->getConfig();

				if ($config["upload.format"]) {
					$imageutils = new $config['thumbnail'];
					$imageutils->formatImage($file1->getAbsolutePath(), $config["upload.format"], $config['upload.autoresize_jpeg_quality']);
				}

				if (checkBool($config["upload.create_thumbnail"]))
					$thumbnail = $this->_createThumb($man, $file1);

				if (checkBool($config['upload.autoresize'])) {
					$ext = getFileExt($file1->getName());

					if (!in_array($ext, array('gif', 'jpeg', 'jpg', 'png')))
						return true;

					$imageInfo = @getimagesize($file1->getAbsolutePath());
					$fileWidth = $imageInfo[0];
					$fileHeight = $imageInfo[1];

					$imageutils = new $config['thumbnail'];
					$percentage = min($config['upload.max_width'] / $fileWidth, $config['upload.max_height'] / $fileHeight);

					if ($percentage <= 1)
						$result = $imageutils->resizeImage($file1->getAbsolutePath(), $file1->getAbsolutePath(), round($fileWidth * $percentage), round($fileHeight * $percentage), $ext, $config['upload.autoresize_jpeg_quality']);
				}
				break;

			case DELETE_ACTION:
				$config = $file1->getConfig();

				if ($config['thumbnail.delete'] == true) {
					$thumbnailFolder = $man->getFile(dirname($file1->getAbsolutePath()) ."/". $config['thumbnail.folder']);
					$thumbnailPath = $thumbnailFolder->getAbsolutePath() . "/" . $config['thumbnail.prefix'] . basename($file1->getAbsolutePath());
					$thumbnail = $man->getFile($thumbnailPath);

					if ($thumbnail->exists())
						$thumbnail->delete();

					// Check if thumbnail directory should be deleted
					if ($thumbnailFolder->exists()) {
						$files = $thumbnailFolder->listFiles();

						if (count($files) == 0)
							$thumbnailFolder->delete();
					}
				}

				break;
		}

		return true; // Pass to next plugin
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
		// Is file and image
		$config = $file->getConfig();
		$input["editable"] = false;

		if ($file->isFile() && ($type == "list" || $type == "insert" || $type == "info")) {
			// Should we get config on each file here?
			//$config = $file->getConfig();
			$ext = getFileExt($file->getName());

			if (!in_array($ext, array('gif', 'jpeg', 'jpg', 'png', 'bmp')))
				return true;

			$imageutils = new $config['thumbnail'];
			$canEdit = $imageutils->canEdit($ext);

			$imageInfo = @getimagesize($file->getAbsolutePath());

			$fileWidth = $imageInfo[0];
			$fileHeight = $imageInfo[1];

			$targetWidth = $config['thumbnail.width'];
			$targetHeight = $config['thumbnail.height'];

			// Check thumnail size
			if ($config['thumbnail.scale_mode'] == "percentage") {
				$percentage = min($config['thumbnail.width'] / $fileWidth, $config['thumbnail.height'] / $fileHeight);

				if ($percentage <= 1) {
					$targetWidth = round($fileWidth * $percentage);
					$targetHeight = round($fileHeight * $percentage);
				} else {
					$targetWidth = $fileWidth;
					$targetHeight = $fileHeight;
				}
			}

			$input["thumbnail"] = true;

			// Check against config.
			if (($config["thumbnail.max_width"] != "" && $fileWidth > $config["thumbnail.max_width"]) || ($config["thumbnail.max_height"] != "" && $fileHeight > $config["thumbnail.max_height"]))
				$input["thumbnail"] = false;
			else {
				$input["twidth"] = $targetWidth;
				$input["theight"] = $targetHeight;
			}

			// Get thumbnail URL
			if ($type == "insert") {
				$thumbFile = $man->getFile($file->getParent() . "/" . $config['thumbnail.folder'] . "/" . $config['thumbnail.prefix'] . $file->getName());

				if ($thumbFile->exists())
					$input["thumbnail_url"] = $man->removeTrailingSlash($config['preview.urlprefix']) . $man->convertPathToURI($thumbFile->getAbsolutePath(), $config["preview.wwwroot"]);
			}

			$input["width"] = $fileWidth;
			$input["height"] = $fileHeight;
			$input["editable"] = $canEdit;
		}

		return true;
	}

	// * * * * * * * Private methods

	/**
	 * SaveImage
	 * TODO: Check for PX or %
	 */
	function _saveImage(&$man, &$file, &$filedata, &$result) {
		$config =& $file->getConfig();

		// Find out if we have a temp file.
		$ext = getFileExt($file->getName());

		if (!$man->isToolEnabled("edit", $config)) {
			trigger_error("{#error.no_access}", FATAL);
			die();
		}

		// To file to save
		if (!$file->exists()) {
			$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), "{#error.file_not_exists}");
			return;
		}

		if (strpos($file->getName(), "mcic_") !== 0)
			$tmpImage = "mcic_". md5(session_id() . $file->getName()) . "." . $ext;
		else
			$tmpImage = $file->getName();

		$tempFile =& $man->getFile(utf8_encode(dirname($file->getAbsolutePath()) . "/" . $tmpImage));
		$tempFile->setTriggerEvents(false);

		/*
		Failed when mcic_ was found due to exclude in filesystem conf
		if ($man->verifyFile($tempFile, "edit") < 0) {
			$result->add("FAILED", $man->encryptPath($tempFile->getAbsolutePath()), $man->getInvalidFileMsg());
			return;
		}
		*/

		// NOTE: add check for R/W

		if ($tempFile->exists()) {
			if ($filedata["target"] != "") {
				$targetfile = $man->getFile($filedata["target"]);

				// Delete format images
				$config = $targetfile->getConfig();
				$imageutils = new $config['thumbnail'];
				$imageutils->deleteFormatImages($targetfile->getAbsolutePath(), $config["upload.format"]);

				// Just ignore it if it's the same file
				if ($tempFile->getAbsolutePath() != $targetfile->getAbsolutePath()) {
					if ($man->verifyFile($targetfile, "edit") < 0) {
						$result->add("FAILED", $man->encryptPath($targetfile->getAbsolutePath()), $man->getInvalidFileMsg());
						return;
					}

					if ($targetfile->exists())
						$targetfile->delete();

					$tempFile->renameTo($targetfile);
					$targetfile->importFile();

					// Reformat
					if ($config["edit.format"]) {
						$imageutils = new $config['thumbnail'];
						$imageutils->formatImage($targetfile->getAbsolutePath(), $config["edit.format"], $config['edit.jpeg_quality']);
					}
				}

				$result->add("OK", $man->encryptPath($targetfile->getAbsolutePath()), "{#message.save_success}");
			} else {
				$file->delete();
				$tempFile->renameTo($file);
				$file->importFile();

				$result->add("OK", $man->encryptPath($file->getAbsolutePath()), "{#message.save_success}");
			}
		} else {
			if ($filedata["target"] != "") {
				$targetfile = $man->getFile($filedata["target"]);

				// Just ignore it if it's the same file
				if ($file->getAbsolutePath() != $targetfile->getAbsolutePath()) {
					if ($man->verifyFile($targetfile, "edit") < 0) {
						$result->add("FAILED", $man->encryptPath($targetfile->getAbsolutePath()), $man->getInvalidFileMsg());
						return;
					}

					if ($targetfile->exists())
						$targetfile->delete();

					$file->copyTo($targetfile);
					$targetfile->importFile();
				}

				$result->add("OK", $man->encryptPath($targetfile->getAbsolutePath()), "{#message.save_success}");
			} else {
				// No temp, no target, abort!
				$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), "{#error.save_failed}");
			}
		}
	}

	/**
	 * CropImage
	 * TODO: Check for PX or %
	 */
	function _cropImage(&$man, &$file, &$filedata, &$result) {
		$ext = getFileExt($file->getName());
		$config = $file->getConfig();
		$imageutils = new $config['thumbnail'];

		if (!$man->isToolEnabled("edit", $config)) {
			trigger_error("{#error.no_access}", FATAL);
			die();
		}

		// To file to crop
		if (!$file->exists()) {
			$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), "{#error.file_not_exists}");
			return;
		}

		if ($filedata["temp"]) {
			if (strpos($file->getName(), "mcic_") !== 0)
				$tmpImage = "mcic_". md5(session_id() . $file->getName()) . "." . $ext;
			else
				$tmpImage = $file->getName();

			$tempFile =& $man->getFile(dirname($file->getAbsolutePath()) . "/" . $tmpImage);
			$tempFile->setTriggerEvents(false);

			$status = $imageutils->cropImage($file->getAbsolutePath(), $tempFile->getAbsolutePath(), $filedata["top"], $filedata["left"], $filedata["width"], $filedata["height"], $ext, $config["edit.jpeg_quality"]);
			if ($status) {
				$tempFile->importFile();
				$result->add("OK", $man->encryptPath($tempFile->getAbsolutePath()), "{#message.crop_success}");
			} else {
				$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), "{#error.crop_failed}");
			}
		} else {
			if (checkBool($config["general.demo"])) {
				$result->add("FAILED", $man->encryptPath($dir->getAbsolutePath()), "{#error.demo}");
				return $result->toArray();
			}

			if ($filedata["target"] != "") {
				$targetfile = $man->getFile($filedata["target"]);

				if ($targetfile->isDirectory()) {
					$targetfile = $man->getFile($man->addTrailingSlash($targetfile->getAbsolutePath()) . $file->getName());
				}

				if ($man->verifyFile($targetfile, "edit") < 0) {
					$result->add("FAILED", $man->encryptPath($targetfile->getAbsolutePath()), $man->getInvalidFileMsg());
					return;
				}
			} else
				$targetfile = $file;

			$status = $imageutils->cropImage($file->getAbsolutePath(), $targetfile->getAbsolutePath(), $filedata["top"], $filedata["left"], $filedata["width"], $filedata["height"], $ext, $config["edit.jpeg_quality"]);

			if ($status) {
				$targetfile->importFile();
				$result->add("OK", $man->encryptPath($targetfile->getAbsolutePath()), "{#message.crop_success}");
			} else {
				$result->add("FAILED", $man->encryptPath($targetfile->getAbsolutePath()), "{#error.no_access}");
			}
		}
	}

	/**
	 * ResizeImage
	 */
	function _resizeImage(&$man, &$file, &$filedata, &$result) {
		$ext = getFileExt($file->getName());
		$config = $file->getConfig();
		$imageutils = new $config['thumbnail'];

		if (!$man->isToolEnabled("edit", $config)) {
			trigger_error("{#error.no_access}", FATAL);
			die();
		}
		
		// To file to resize
		if (!$file->exists()) {
			$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), "{#error.file_not_exists}");
			return;
		}

		if ($filedata["temp"]) {
			if (strpos($file->getName(), "mcic_") !== 0)
				$tmpImage = "mcic_". md5(session_id() . $file->getName()) . "." . $ext;
			else
				$tmpImage = $file->getName();

			$tempFile =& $man->getFile(dirname($file->getAbsolutePath()) . "/" . $tmpImage);
			$tempFile->setTriggerEvents(false);
			
			$status = $imageutils->resizeImage($file->getAbsolutePath(), $tempFile->getAbsolutePath(), $filedata["width"], $filedata["height"], $ext, $config["edit.jpeg_quality"]);
			if ($status) {
				$tempFile->importFile();
				$result->add("OK", $man->encryptPath($tempFile->getAbsolutePath()), "{#message.resize_success}");
			} else {
				$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), "{#error.resize_failed}");
			}
		} else {
			if (checkBool($config["general.demo"])) {
				$result->add("FAILED", $man->encryptPath($dir->getAbsolutePath()), "{#error.demo}");
				return $result->toArray();
			}

			if ($filedata["target"] != "") {
				$targetfile = $man->getFile($filedata["target"]);

				if ($targetfile->isDirectory()) {
					$targetfile = $man->addTrailingSlash($man->getFile($targetfile->getAbsolutePath()) . $file->getName());
				}

				if ($man->verifyFile($targetfile, "edit") < 0) {
					$result->add("FAILED", $man->encryptPath($targetfile->getAbsolutePath()), $man->getInvalidFileMsg());
					return;
				}
			} else
				$targetfile = $file;

			$status = $imageutils->resizeImage($file->getAbsolutePath(), $targetfile->getAbsolutePath(), $filedata["width"], $filedata["height"], $ext, $config["edit.jpeg_quality"]);

			if ($status) {
				$targetfile->importFile();
				$result->add("OK", $man->encryptPath($targetfile->getAbsolutePath()), "{#message.resize_success}");
			} else {
				$result->add("FAILED", $man->encryptPath($targetfile->getAbsolutePath()), "{#error.resize_failed}");
			}
		}
	}

	/**
	 * RotateImage
	 */
	function _rotateImage(&$man, &$file, &$filedata, &$result) {
		$ext = getFileExt($file->getName());
		$config = $file->getConfig();
		$imageutils = new $config['thumbnail'];

		if (!$man->isToolEnabled("edit", $config)) {
			trigger_error("{#error.no_access}", FATAL);
			die();
		}

		// To file to rotate
		if (!$file->exists()) {
			$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), "{#error.file_not_exists}");
			return;
		}

		if ($filedata["temp"]) {
			if (strpos($file->getName(), "mcic_") !== 0)
				$tmpImage = "mcic_". md5(session_id() . $file->getName()) . "." . $ext;
			else
				$tmpImage = $file->getName();

			$tempFile =& $man->getFile(dirname($file->getAbsolutePath()) . "/" . $tmpImage);
			$tempFile->setTriggerEvents(false);

			$status = $imageutils->rotateImage($file->getAbsolutePath(), $tempFile->getAbsolutePath(), $ext, $filedata["angle"], $config["edit.jpeg_quality"]);
			if ($status) {
				$tempFile->importFile();
				$result->add("OK", $man->encryptPath($tempFile->getAbsolutePath()), "{#message.rotate_success}");
			} else {
				$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), "{#error.rotate_failed}");
			}
		} else {
			if (checkBool($config["general.demo"])) {
				$result->add("FAILED", $man->encryptPath($dir->getAbsolutePath()), "{#error.demo}");
				return $result->toArray();
			}

			if ($filedata["target"] != "") {
				$targetfile = $man->getFile($filedata["target"]);

				if ($targetfile->isDirectory()) {
					$targetfile = $man->addTrailingSlash($man->getFile($targetfile->getAbsolutePath()) . $file->getName());
				}

				if ($man->verifyFile($targetfile, "edit") < 0) {
					$result->add("FAILED", $man->encryptPath($targetfile->getAbsolutePath()), $man->getInvalidFileMsg());
					return;
				}
			} else
				$targetfile = $file;

			$status = $imageutils->rotateImage($file->getAbsolutePath(), $targetfile->getAbsolutePath(), $ext, $filedata["angle"], $config["edit.jpeg_quality"]);

			if ($status) {
				$targetfile->importFile();
				$result->add("OK", $man->encryptPath($targetfile->getAbsolutePath()), "{#message.rotate_success}");
			} else {
				$result->add("FAILED", $man->encryptPath($targetfile->getAbsolutePath()), "{#error.rotate_failed}");
			}
		}
	}

	/**
	 * FlipImage
	 */
	function _flipImage(&$man, &$file, &$filedata, &$result) {
		$ext = getFileExt($file->getName());
		$config = $file->getConfig();
		$imageutils = new $config['thumbnail'];

		if (!$man->isToolEnabled("edit", $config)) {
			trigger_error("{#error.no_access}", FATAL);
			die();
		}

		// To file to flip
		if (!$file->exists()) {
			$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), "{#error.file_not_exists}");
			return;
		}

		if ($filedata["temp"]) {
			if (strpos($file->getName(), "mcic_") !== 0)
				$tmpImage = "mcic_". md5(session_id() . $file->getName()) . "." . $ext;
			else
				$tmpImage = $file->getName();

			$tempFile =& $man->getFile(dirname($file->getAbsolutePath()) . "/" . $tmpImage);
			$tempFile->setTriggerEvents(false);
			
			$status = $imageutils->flipImage($file->getAbsolutePath(), $tempFile->getAbsolutePath(), $ext, $filedata["vertical"], $filedata["horizontal"]);
			if ($status) {
				$tempFile->importFile();
				$result->add("OK", $man->encryptPath($tempFile->getAbsolutePath()), "{#message.flip_success}");
			} else {
				$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), "{#error.flip_failed}");
			}
		} else {
			if (checkBool($config["general.demo"])) {
				$result->add("FAILED", $man->encryptPath($dir->getAbsolutePath()), "{#error.demo}");
				return $result->toArray();
			}

			if ($filedata["target"] != "") {
				$targetfile = $man->getFile($filedata["target"]);

				if ($targetfile->isDirectory()) {
					$targetfile = $man->addTrailingSlash($man->getFile($targetfile->getAbsolutePath()) . $file->getName());
				}

				if ($man->verifyFile($targetfile, "edit") < 0) {
					$result->add("FAILED", $man->encryptPath($targetfile->getAbsolutePath()), $man->getInvalidFileMsg());
					return;
				}
			} else
				$targetfile = $file;

			$status = $imageutils->flipImage($file->getAbsolutePath(), $targetfile->getAbsolutePath(), $ext, $filedata["vertical"], $filedata["horizontal"], $config["edit.jpeg_quality"]);

			if ($status) {
				$targetfile->importFile();
				$result->add("OK", $man->encryptPath($targetfile->getAbsolutePath()), "{#message.flip_success}");
			} else {
				$result->add("FAILED", $man->encryptPath($targetfile->getAbsolutePath()), "{#error.flip_failed}");
			}
		}
	}

	/**
	 * Clean up temp files, input is dir path.
	 */
	function _cleanUp(&$man, $path) {
		$filedir =& $man->getFile($path);
		$config = $filedir->getConfig();

		if ($man->verifyFile($filedir, "edit") < 0)
			return;

		// If we can't access this dir, we just return.
		if (!$filedir->canWrite())
			return;

		// Delete old files
		$files = $filedir->listFiles();
		foreach ($files as $file) {
			if (strpos($file->getName(), "mcic_") === 0 && time() - $file->getLastModified() > 3600) {
				$file->setTriggerEvents(false);
				$file->delete();
			}
		}
	}

	/**
	 * Lists file.
	 */
	function _getMediaInfo(&$man, $input) {
		// Convert URL to path
		if (isset($input["url"])) {
			$url = parse_url($input["url"]);
			$input["path"] = $man->resolveURI($url["path"]);

			if (!$man->verifyPath($input["path"]))
				trigger_error(sprintf("Could not resolve URL: %s to a filesystem path. Could be that the image is outside the configured filesystem.rootpath.", $input["url"]), FATAL);
		}

		$file =& $man->getFile($input["path"]);
		$config = $file->getConfig();
		$parent =& $file->getParentFile();
		$files = array();

		if ($parent->isDirectory()) {
			// Setup file filter
			$fileFilter = new Moxiecode_BasicFileFilter();
			//$fileFilter->setDebugMode(true);
			$fileFilter->setIncludeDirectoryPattern($config['filesystem.include_directory_pattern']);
			$fileFilter->setExcludeDirectoryPattern($config['filesystem.exclude_directory_pattern']);
			$fileFilter->setIncludeFilePattern($config['filesystem.include_file_pattern']);
			$fileFilter->setExcludeFilePattern($config['filesystem.exclude_file_pattern']);
			$fileFilter->setIncludeExtensions($config['filesystem.extensions']);
			$fileFilter->setOnlyFiles(true);

			// List files
			$files =& $parent->listFilesFiltered($fileFilter);
		}

		$match = false;
		$prev = "";
		$next = "";

		foreach($files as $curfile) {
			if ($curfile->getAbsolutePath() == $file->getAbsolutePath()) {
				$match = true;
				continue;
			} else if (!$match)
				$prev = $curfile->getAbsolutePath();

			if ($match) {
				$next = $curfile->getAbsolutePath();
				break;
			}
			
		}
		
		$ext = getFileExt($file->getName());

		// Input default size?
		$width = "425";
		$height = "350";

		// All types that getimagesize support 
		$imagearray = array('gif', 'jpg', 'png', 'swf', 'psd', 'bmp', 'tiff', 'jpc', 'jp2', 'jpx', 'jb2', 'swc', 'iff', 'wbmp', 'xbm');

		if (in_array($ext, $imagearray)) {
			$sizeinfo = @getimagesize($file->getAbsolutePath());
			if ($sizeinfo) {
				$width = $sizeinfo[0];
				$height = $sizeinfo[1];
			}
		}

		$result = new Moxiecode_ResultSet("name,path,url,size,type,created,modified,width,height,attribs,next,prev,custom");
		$custom = array();
		$man->dispatchEvent("onCustomInfo", array(&$file, "info", &$custom));
		$attribs = ($file->canRead() && checkBool($config["filesystem.readable"]) ? "R" : "-") . ($file->canWrite() && checkBool($config["filesystem.writable"]) ? "W" : "-");
		$url = $man->removeTrailingSlash($config['preview.urlprefix']) . $man->convertPathToURI($file->getAbsolutePath(), $config['preview.wwwroot']);

		$result->add(
			utf8_encode($file->getName()),
			$man->encryptPath($file->getAbsolutePath()),
			utf8_encode($url),
			$file->getLength(),
			$ext,
			date($config['filesystem.datefmt'], $file->getCreationDate()),
			date($config['filesystem.datefmt'], $file->getLastModified()),
			$width,
			$height,
			$attribs,
			$man->encryptPath($next),
			$man->encryptPath($prev),
			$custom);

		return $result->toArray();
	}

	function _createThumb(&$man, &$file) {
		$config = $file->getConfig();
		$imageutils = new $config['thumbnail'];
		$ext = getFileExt($file->getName());
		$canEdit = $imageutils->canEdit($ext);

		if (!in_array($ext, array('gif', 'jpeg', 'jpg', 'png', 'bmp')))
			return false;

		// Check if we have an EXIF JPG file.
		if (($config['thumbnail.use_exif'] == true) && (function_exists("exif_thumbnail")) && (strtolower($ext) == "jpg" || strtolower($ext) == "jpeg")) {
			$image = @exif_thumbnail($file->getAbsolutePath(), $exif_width, $exif_height, $exif_type);

			if ($image !== false)
				return false;
		}

		// Check for thumbnails
		$havethumbnail = true;

		// No folder or not enabled = no thumbnail, could check for thumbnail folder and delete it perhaps?
		if (($config['thumbnail.folder'] == "") || ($config['thumbnail.enabled'] == false))
			$havethumbnail = false;

		// If we dont have GD we dont have a thumbnail.
		if (!$canEdit)
			$havethumbnail = false;

		// Check so that we aren't inside a thumbnail folder.
		$parentFile = $file->getParentFile();
		if ($config['thumbnail.folder'] == $parentFile->getName())
			$havethumbnail = false;

		// Ok, no thumbnail? Then lets just stream the original image.
		if (!$havethumbnail)
			return false;

		// Ok, we have a thumbnail or should generate one, now lets check some stuff about it.
		$thumbnailFolder = $man->getFile(dirname($file->getAbsolutePath()) ."/". $config['thumbnail.folder']);
		
		if ((!$thumbnailFolder->exists()) && ($config['thumbnail.auto_generate'] == true))
			$thumbnailFolder->mkdir();

		$thumbnailPath = $thumbnailFolder->getAbsolutePath() . "/" . $config['thumbnail.prefix'] . basename($file->getAbsolutePath());
		$thumbnail = $man->getFile(utf8_encode($thumbnailPath));
		$thumbnail->setTriggerEvents(false);
		$thumbnailQuality = $config['thumbnail.jpeg_quality'];
		$thumbnailResult = false;

		$imageInfo = @getimagesize($file->getAbsolutePath());
		$fileWidth = $imageInfo[0];
		$fileHeight = $imageInfo[1];
		$fileType = $imageInfo[2];
		$thumbnailType = $fileType;

		// Calculate thumbnail width and height
		$targetWidth = $config['thumbnail.width'];
		$targetHeight = $config['thumbnail.height'];

		if ($config['thumbnail.scale_mode'] == "percentage") {
			$percentage = min($config['thumbnail.width'] / $fileWidth, $config['thumbnail.height'] / $fileHeight);

			if ($percentage <= 1) {
				$targetWidth = round($fileWidth * $percentage);
				$targetHeight = round($fileHeight * $percentage);
			} else {
				$targetWidth = $fileWidth;
				$targetHeight = $fileHeight;
			}
		}

		if ($thumbnail->exists()) {
			$thumbnailInfo = @getimagesize($thumbnail->getAbsolutePath());
			$thumbnailWidth = $thumbnailInfo[0];
			$thumbnailHeight = $thumbnailInfo[1];
			$thumbnailType = $thumbnailInfo[2];

			// Check width and height against config
			//debug($thumbnailHeight . " - " . $targetHeight . " - " . $thumbnailWidth . " - " . $targetWidth);
			if (($thumbnailHeight != $targetHeight) || ($thumbnailWidth != $targetWidth)) {
				$thumbnail->delete();
				$thumbnailResult = $imageutils->resizeImage($file->getAbsolutePath(), $thumbnail->getAbsolutePath(), $targetWidth, $targetHeight, $ext, $thumbnailQuality);
			}

			// Check modificationdate against original image
			if ($file->getLastModified() != $thumbnail->getLastModified()) {
				$thumbnail->delete();
				$thumbnailResult = $imageutils->resizeImage($file->getAbsolutePath(), $thumbnail->getAbsolutePath(), $targetWidth, $targetHeight, $ext, $thumbnailQuality);
			}

			if ($thumbnailResult)
				$thumbnail->setLastModified($file->getLastModified());
		
		} else if ((!$thumbnail->exists()) && ($config['thumbnail.auto_generate'] == true)) {
			$thumbnailResult = $imageutils->resizeImage($file->getAbsolutePath(), $thumbnail->getAbsolutePath(), $targetWidth, $targetHeight, $ext, $thumbnailQuality);
			if ($thumbnailResult) {
				$thumbnailInfo = @getimagesize($thumbnail->getAbsolutePath());
				$thumbnailType = $thumbnailInfo[2];
				$thumbnail->setLastModified($file->getLastModified());
				$thumbnail->importFile();
			}
		}

		// failsafe check
		if ($thumbnail->exists() && $thumbnailType)
			return $thumbnail;

		return false;
	}

	function _streamThumb(&$man, $input) {
		if (!$man->verifyPath($input["path"]) < 0) {
			trigger_error("Path verification failed.", FATAL);
			die();
		}

		$path = $man->decryptPath($input["path"]);
		$file =& $man->getFile($path);
		$ext = getFileExt($file->getName());

		$config = $file->getConfig();

		$urlprefix = $man->toUnixPath($config['preview.urlprefix']);
		$urlsuffix = $man->toUnixPath($config['preview.urlsuffix']);

		// NOTE: Verify more stuff here before proceeding.
		if ($man->verifyFile($file, "thumbnail", $config) < 0) {
			trigger_error("Path verification failed.", FATAL);
			die();
		}

		//$imageutils = new $config['thumbnail'];
		//$canEdit = $imageutils->canEdit($ext);

		// Check if we have an EXIF JPG file.
		if (($config['thumbnail.use_exif'] == true) && (function_exists("exif_thumbnail")) && (strtolower($ext) == "jpg" || strtolower($ext) == "jpeg")) {
			$image = @exif_thumbnail($file->getAbsolutePath(), $exif_width, $exif_height, $exif_type);

			if ($image !== false) {
				header('Content-type: '. image_type_to_mime_type($exif_type));
				echo $image;
				return null;
			}
		}

		$thumbnail = $this->_createThumb($man, $file);

		if ($thumbnail != false) {
			header('Content-type: ' . mapMimeTypeFromUrl($thumbnail->getName(), "../" . $config['stream.mimefile']));

			if (!readfile($thumbnail->getAbsolutePath()))
				header("Location: ". $urlprefix . $man->convertPathToURI($thumbnail->getAbsolutePath(), $config['preview.wwwroot']) . $urlsuffix);
		} else {
			header('Content-type: ' . mapMimeTypeFromUrl($file->getName(), "../" . $config['stream.mimefile']));

			if (!readfile($file->getAbsolutePath()))
				header("Location: " . $urlprefix . $man->convertPathToURI($file->getAbsolutePath(), $config['preview.wwwroot']) . $urlsuffix);
		}

		return null;
	}

	/**#@-*/
}

// Add plugin to MCManager
$man->registerPlugin("imagemanager", new Moxiecode_ImageManagerPlugin(), "im");

?>