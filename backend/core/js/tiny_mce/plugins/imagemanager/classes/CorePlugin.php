<?php
/**
 * $Id: CorePlugin.php 751 2009-10-20 12:05:36Z spocke $
 *
 * @package MCManagerCore
 * @author Moxiecode
 * @copyright Copyright © 2007, Moxiecode Systems AB, All rights reserved.
 */

// Load local file system
require_once(MCMANAGER_ABSPATH . "FileSystems/LocalFileImpl.php");
require_once(MCMANAGER_ABSPATH . "FileSystems/RootFileImpl.php");

/**
 * This plugin contains the Core logic shared between manager products.
 *
 * @package MCManagerCore
 */
class Moxiecode_CorePlugin extends Moxiecode_ManagerPlugin {
	/**#@+
	 * @access public
	 */

	/**
	 * Constructs a new MCManagerCore instance.
	 */
	function Moxiecode_CorePlugin() {
	}

	/**
	 * Register file system.
	 */
	function onInit(&$man) {
		$config = $man->getConfig();

		// Register local and root file system
		$man->registerFileSystem('file', isset($config['filesystem']) ? $config['filesystem'] : 'Moxiecode_LocalFileImpl');
		$man->registerFileSystem('root', 'Moxiecode_RootFileImpl');

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
			case "deleteFiles":
				return $this->_deleteFile($man, $input);

			case "listFiles":
				return $this->_listFiles($man, $input);

			case "createDirs":
				return $this->_createDirs($man, $input);

			case "getConfig":
				return $this->_getConfig($man, $input);

			case "insertFiles":
				return $this->_insertFiles($man, $input);

			case "loopBack":
				return $this->_loopBack($input);

			case "keepAlive":
				return $this->_keepAlive($man, $input);
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
		$config = $man->getConfig();

		// Download stream
		if ($cmd == "download") {
			if ($man->verifyPath($input["path"])) {
				$file =& $man->getFile($input["path"]);
				$config = $file->getConfig();

				if ($man->verifyFile($file, "download") > 0 && $file->exists()) {
					// Get the mimetype, need to go to ../ parent folder cause... well we have to.
					//$mimeType = mapMimeTypeFromUrl($file->getAbsolutePath(), "../". $config['stream.mimefile']);

					// These seems to be needed for some IE proxies
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Cache-Control: private", false);

					header("Content-type: application/octet-stream");
					header("Content-Disposition: attachment; filename=\"" . $file->getName() . "\"");

					// Stream data
					$stream =& $file->open('rb');
					if ($stream) {
						while (($buff = $stream->read()) != null)
							echo $buff;

						$stream->close();
					}

					return false;
				}
			} else {
				header('HTTP/1.0 404 Not found');
				header('status: 404 Not found');

				echo "Requested resource could not be found. Or access was denied.";
				die();
			}

			// Do not pass to next
			return false;
		}

		// Normal stream
		if ($cmd == "streamFile") {
			if (!$man->verifyPath($input["path"]) < 0) {
				trigger_error("Path verification failed.", FATAL);
				die();
			}

			$file = $man->getFile($input["path"]);
			$config = $file->getConfig();

			if (!$file->exists()) {
				trigger_error("File not found.", FATAL);
				die();
			} else {
				if (getClassName($file) == 'moxiecode_localfileimpl') {
					// Redirect to data
					$url = $man->removeTrailingSlash($config['preview.urlprefix']) . $man->convertPathToURI($file->getParent() . "/" . str_replace("+", "%20", urlencode($file->getName())), $config["preview.wwwroot"]) . $config['preview.urlsuffix'];

					// Passthrough rnd
					if (isset($input["rnd"]))
						$url .= (strpos($url, "?") === false ? "?" : "&") . "rnd=" . $input["rnd"];

					header('location: ' . $url);
					die();
				} else {
					// Verify that we can stream this one
					if ($man->verifyFile($file, "stream") < 0) {
						header('HTTP/1.0 404 Not found');
						header('status: 404 Not found');

						echo "Requested resource could not be found. Or access was denied.";
						die();
					}

					// Get the mimetype, need to go to ../ parent folder cause... well we have to.
					$mimeType = mapMimeTypeFromUrl($file->getAbsolutePath(), "../". $config['stream.mimefile']);
					header("Content-type: " . $mimeType);

					// Stream data
					$stream =& $file->open('rb');
					if ($stream) {
						while (($buff = $stream->read()) != null)
							echo $buff;

						$stream->close();
					}
				}

				return false;
			}
		}

		// Devkit commands

		switch ($cmd) {
			case "viewServerInfo":
				if (!checkBool($config['general.debug']))
					die("You have to enable debugging in config by setting general.debug to true.");

				phpinfo();

				break;

			case "downloadServerInfo":
				if (!checkBool($config['general.debug']))
					die("You have to enable debugging in config by setting general.debug to true.");

				// Get all ini settings
				$data = ini_get_all();

				// Setup all headers
				header("Content-type: text/plain; charset=UTF-8");
				header("Content-Disposition: attachment; filename=dump.txt");
				header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Cache-Control: no-store, no-cache, must-revalidate");
				header("Cache-Control: post-check=0, pre-check=0", false);
				header("Pragma: no-cache");

				echo "# Config from config.php" . "\r\n\r\n";
				foreach ($config as $key => $value) {
					if (is_bool($value))
						echo $key . "=" . ($value ? "true" : "false") . "\r\n";
					else
						echo $key . "=" . $value . "\r\n";
				}

				// Dump INI settings
				echo "\r\n# PHP INI settings file\r\n\r\n";

				foreach ($data as $key => $value)
					echo $key . "=" . $value['local_value'] . "\r\n";

				// Dump function support
				echo "\r\n# Function check" . "\r\n\r\n";

				$functions = array(
					"ImagecreateFromJpeg",
					"ImageJpeg",
					"ImagecreateFromGif",
					"ImageGif",
					"ImagecreateFromPng",
					"ImagePng",
					"gzdeflate",
					"gzinflate"
				);

				foreach ($functions as $function)
					echo $function . "=" . (function_exists($function) ? "ok" : "missing") . "\r\n";

				// Dump rootpath access
				echo "\r\n# Rootpath access" . "\r\n\r\n";

				foreach ($man->getRootPaths() as $rootpath) {
					$stat = stat($rootpath);
					echo $rootpath . "\r\n";
					echo "  is_readable=" . (is_readable($rootpath) ? "readable" : "not readable") . "\r\n";
					echo "  is_writable=" . (is_writable($rootpath) ? "writable" : "not writable") . "\r\n";
					foreach ($stat as $key => $value)
						echo "  " . $key . "=" . $value . "\r\n";
				}

				break;

			case "viewLog":
				if (!checkBool($config['general.debug']))
					die("You have to enable debugging in config by setting general.debug to true.");

				header('Content-type: text/plain');

				if ($input['level'] == "debug")
					echo @file_get_contents("../logs/debug.log");
				else
					echo @file_get_contents("../logs/error.log");

				break;

			case "clearLog":
				header('Content-type: text/plain');

				if (!checkBool($config['general.debug']))
					die("You have to enable debugging in config by setting general.debug to true.");

				if ($input['level'] == "debug")
					$log = "../logs/debug.log";
				else
					$log = "../logs/error.log";

				@unlink($log);
				for ($i=0; $i<10; $i++)
					@unlink($log . "." . $i);

				echo "Logs cleared.";
				break;
		}

		// Pass to next
		return true;
	}

	/**
	 * Gets called when data is streamed/uploaded from client. This method should take care of
	 * any uploaded files and move them to the correct location.
	 *
	 * @param MCManager $man MCManager reference that the plugin is assigned to.
	 * @param string $cmd Upload command that is to be performed.
	 * @param string $input Array of input arguments.
	 * @return object Result object data or null if the event wasn't handled.
	 */
	function onUpload(&$man, $cmd, $input) {
		if ($cmd  == "upload") {
			// Setup response
			$result = new Moxiecode_ResultSet("status,file,message");
			$path = $man->decryptPath($input["path"]);
			$config = $man->getConfig();

			if ($man->verifyPath($path)) {
				$file =& $man->getFile($path);
				$config = $file->getConfig();

				$maxSizeBytes = preg_replace("/[^0-9]/", "", $config["upload.maxsize"]);

				if (strpos((strtolower($config["upload.maxsize"])), "k") > 0)
					$maxSizeBytes *= 1024;

				if (strpos((strtolower($config["upload.maxsize"])), "m") > 0)
					$maxSizeBytes *= (1024 * 1024);

				// Is chunked upload
				if (isset($input["chunk"])) {
					$filename = $input["name"];
					$chunk = intval($input["chunk"]);
					$chunks = intval($input["chunks"]);

					// No access, tool disabled
					if (in_array("upload", explode(',', $config['general.disabled_tools'])) || !$file->canWrite() || !checkBool($config["filesystem.writable"])) {
						$result->add("ACCESS_ERROR", $man->encryptPath($file->getAbsolutePath()), "{#error.no_access}");
						return $result;
					}

					$file =& $man->getFile($path, $filename, MC_IS_FILE);
					if ($man->verifyFile($file, "upload") < 0) {
						$result->add("ACCESS_ERROR", $man->encryptPath($file->getAbsolutePath()), $man->getInvalidFileMsg());
						return $result;
					}

					// Hack attempt
					if ($filename == $config['filesystem.local.access_file_name']) {
						$result->add("MCACCESS_ERROR", $man->encryptPath($file->getAbsolutePath()), "{#error.no_access}");
						return $result;
					}

					// Only peform IO when not in demo mode
					if (!checkBool($config['general.demo'])) {
						if ($chunk == 0 && $file->exists() && (!isset($config["upload.overwrite"]) || $config["upload.overwrite"] == false)) {
							$result->add("OVERWRITE_ERROR", $man->encryptPath($file->getAbsolutePath()), "{#error.file_exists}");
							return $result;
						}

						if ($chunk == 0 && $file->exists() && $config["upload.overwrite"] == true)
							$file->delete();

						// Write file
						$stream =& $file->open($chunk == 0 ? 'wb' : 'ab');
						if ($stream) {
							$in = fopen("php://input", "rb");
							if ($in) {
								while ($buff = fread($in, 4096))
									$stream->write($buff);
							}

							$stream->close();
						}

						// Check file size
						if ($file->getLength() > $maxSizeBytes) {
							$file->delete();
							$result->add("SIZE_ERROR", $man->encryptPath($file->getAbsolutePath()), "{#error.error_to_large}");
							return $result;
						}

						// Verify uploaded file, if it fails delete it
						$status = $man->verifyFile($file, "upload");
						if ($status < 0) {
							$file->delete();
							$result->add("FILTER_ERROR", $man->encryptPath($file->getAbsolutePath()), $man->getInvalidFileMsg());
							return $result;
						}

						// Import file when all chunks are complete
						if ($chunk == $chunks - 1) {
							clearstatcache();
							debug($chunk, $chunks, filesize($file->getAbsolutePath()), $chunk == 0 ? 'wb' : 'ab');
							$file->importFile();
						}
					}

					$result->add("OK", $man->encryptPath($file->getAbsolutePath()), "{#message.upload_ok}");

					return $result;
				} else {
					// Ok lets check the files array out.
					for ($i=0; isset($_FILES['file' . $i]['tmp_name']); $i++) {
						$filename = utf8_encode($input["name" . $i]);

						// Do nothing in demo mode
						if (checkBool($config['general.demo'])) {
							$result->add("DEMO_ERROR", $man->encryptPath($file->getAbsolutePath()), "{#error.demo}");
							continue;
						}

						// No access, tool disabled
						if (in_array("upload", explode(',', $config['general.disabled_tools'])) || !$file->canWrite() || !checkBool($config["filesystem.writable"])) {
							$result->add("ACCESS_ERROR", $man->encryptPath($file->getAbsolutePath()), "{#error.no_access}");
							continue;
						}

						// Get ext to glue back on
						$ext = "";
						if (strpos(basename($_FILES['file' . $i]['name']), ".") > 0) {
							$ar = explode('.', basename($_FILES['file' . $i]['name']));
							$ext = array_pop($ar);
						}

						$file =& $man->getFile($path, $filename . "." . $ext, "", MC_IS_FILE);
						if ($man->verifyFile($file, "upload") < 0) {
							$result->add("ACCESS_ERROR", $man->encryptPath($file->getAbsolutePath()), $man->getInvalidFileMsg());
							continue;
						}

						$config = $file->getConfig();

						if (is_uploaded_file($_FILES['file' . $i]['tmp_name'])) {
							// Hack attempt
							if ($filename == $config['filesystem.local.access_file_name']) {
								@unlink($_FILES['file' . $i]['tmp_name']);
								$result->add("MCACCESS_ERROR", $man->encryptPath($file->getAbsolutePath()), "{#error.no_access}");
								continue;
							}

							if ($file->exists() && (!isset($config["upload.overwrite"]) || $config["upload.overwrite"] == false)) {
								@unlink($_FILES['file' . $i]['tmp_name']);
								$result->add("OVERWRITE_ERROR", $man->encryptPath($file->getAbsolutePath()), "{#error.file_exists}");
								continue;
							}

							if ($file->exists() && $config["upload.overwrite"] == true)
								$file->delete();

							if (getClassName($file) == 'moxiecode_localfileimpl') {
								if (!move_uploaded_file($_FILES['file' . $i]['tmp_name'], $file->getAbsolutePath())) {
									$result->add("RW_ERROR", $man->encryptPath($file->getAbsolutePath()), "{#error.upload_failed}");
									continue;
								}

								// Dispatch add event
								$file->importFile();
							} else
								$file->importFile($_FILES['file' . $i]['tmp_name']);

							if ($file->getLength() > $maxSizeBytes) {
								$file->delete();
								$result->add("SIZE_ERROR", $man->encryptPath($file->getAbsolutePath()), "{#error.error_to_large}");
								continue;
							}

							// Verify uploaded file, if it fails delete it
							$status = $man->verifyFile($file, "upload");
							if ($status < 0) {
								$file->delete();
								$result->add("FILTER_ERROR", $man->encryptPath($file->getAbsolutePath()), $man->getInvalidFileMsg());
								continue;
							}

							$result->add("OK", $man->encryptPath($file->getAbsolutePath()), "{#message.upload_ok}");
						} else
							$result->add("GENERAL_ERROR", $man->encryptPath($file->getAbsolutePath()), "{#error.upload_failed}");
					}
				}
			} else
				$result->add("PATH_ERROR", $man->encryptPath($path), "{#error.upload_failed}");

			return $result;
		}
	}

	// * * * * * * * * Private methods

	function _deleteFile(&$man, &$input) {
		$result = new Moxiecode_ResultSet("status,file,message");

		for ($i=0; isset($input["path" . $i]); $i++) {
			$file =& $man->getFile($input["path" . $i]);
			$config = $file->getConfig();

			if (checkBool($config['general.demo'])) {
				$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), "{#error.demo}");
				continue;
			}

			if (!$man->isToolEnabled("delete", $config)) {
				trigger_error("{#error.no_access}", FATAL);
				die();
			}

			if (!$file->exists()) {
				$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), "{#error.file_not_exists}");
				continue;
			}

			if ($man->verifyFile($file, "delete") < 0) {
				$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), $man->getInvalidFileMsg());
				continue;
			}

			if (!$file->canWrite()) {
				$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), "{#error.no_write_access}");
				continue;
			}

			if (!checkBool($config["filesystem.writable"])) {
				$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), "{#error.no_write_access}");
				continue;
			}

			if ($file->delete($config['filesystem.delete_recursive']))
				$result->add("OK", $man->encryptPath($file->getAbsolutePath()), "{#message.delete_success}");
			else
				$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), "{#error.delete_failed}");
		}

		return $result->toArray();
	}

	function _insertFiles(&$man, $input) {
		$result = new Moxiecode_ResultSet("name,path,url,size,type,created,modified,attribs,custom");
		$indata = array();

		for ($i=0; isset($input['path' . $i]); $i++) {
			$custom = array();
			$file = $man->getFile($input["path". $i]);

			if (!$file->exists()) {
				trigger_error("{#error.file_not_exists}", FATAL);
				die;
			}

			$ar = explode('.', $file->getName());
			$ext = array_pop($ar);

			$man->dispatchEvent("onCustomInfo", array(&$file, "insert", &$custom));

			$status = $man->dispatchEvent("onInsertFile", array(&$file));
			$config = $file->getConfig();

			$attribs = ($file->canRead() && checkBool($config["filesystem.readable"]) ? "R" : "-") . ($file->canWrite() && checkBool($config["filesystem.writable"]) ? "W" : "-");
			$url = $man->removeTrailingSlash($config['preview.urlprefix']) . $man->convertPathToURI($file->getAbsolutePath(), $config["preview.wwwroot"]);

			$result->add($file->getName(), $man->encryptPath($file->getAbsolutePath()), utf8_encode($url), $file->getLength(), $ext, $file->getCreationDate(), $file->getLastModified(), $attribs, $custom);
		}

		return $result->toArray();
	}

	function _listDefault(&$man, $file, $input, &$result, $filter_root_path) {
		$config = $man->getConfig();

		// Setup input file filter
		$inputFileFilter = new Moxiecode_BasicFileFilter();

		if (isset($input['include_directory_pattern']) && $input['include_directory_pattern'])
			$inputFileFilter->setIncludeDirectoryPattern($input['include_directory_pattern']);

		if (isset($input['exclude_directory_pattern']) && $input['exclude_directory_pattern'])
			$inputFileFilter->setExcludeDirectoryPattern($input['exclude_directory_pattern']);

		if (isset($input['include_file_pattern']) && $input['include_file_pattern'])
			$inputFileFilter->setIncludeFilePattern($input['include_file_pattern']);

		if (isset($input['exclude_file_pattern']) && $input['exclude_file_pattern'])
			$inputFileFilter->setExcludeFilePattern($input['exclude_file_pattern']);

		if (isset($input['extensions']) && $input['extensions'])
			$inputFileFilter->setIncludeExtensions($input['extensions']);

		// If file doesn't exists use default path
		if (!$file->exists()) {
			$file = $man->getFile($config['filesystem.path']);

			$result->setHeader("path", $man->encryptPath($file->getAbsolutePath()));
			$result->setHeader("visual_path", checkBool($config['general.user_friendly_paths']) ? $man->toVisualPath($file->getAbsolutePath()) : $man->encryptPath($file->getAbsolutePath()));
		}

		// List files
		$config = $file->getConfig();

		if ($file->isDirectory()) {
			// Setup file filter
			$fileFilter = new Moxiecode_BasicFileFilter();
			//$fileFilter->setDebugMode(true);
			$fileFilter->setIncludeDirectoryPattern($config['filesystem.include_directory_pattern']);
			$fileFilter->setExcludeDirectoryPattern($config['filesystem.exclude_directory_pattern']);
			$fileFilter->setIncludeFilePattern($config['filesystem.include_file_pattern']);
			$fileFilter->setExcludeFilePattern($config['filesystem.exclude_file_pattern']);
			$fileFilter->setIncludeExtensions($config['filesystem.extensions']);

			// If file is hidden then try the parent
			if ($fileFilter->accept($file) <= 0) {
				$file = $file->getParentFile();

				$result->setHeader("path", $man->encryptPath($file->getAbsolutePath()));
				$result->setHeader("visual_path", checkBool($config['general.user_friendly_paths']) ? $man->toVisualPath($file->getAbsolutePath()) : $man->encryptPath($file->getAbsolutePath()));
			}

			if (isset($input["filter"]) && $input["filter"] != null)
				$fileFilter->setIncludeWildcardPattern($input["filter"]);

			if (isset($input["only_dirs"]) && checkBool($input["only_dirs"]))
				$fileFilter->setOnlyDirs(true);
			else if (!checkBool($config["filesystem.list_directories"], true) || (isset($input["only_files"]) && checkBool($input["only_files"])))
				$fileFilter->setOnlyFiles(true);

			// List files
			$combinedFilter = new Moxiecode_CombinedFileFilter();
			$combinedFilter->addFilter($fileFilter);
			$combinedFilter->addFilter($inputFileFilter);

			$files =& $file->listFilesFiltered($combinedFilter);

			$showparent = isset($input["no_parent"]) ? checkBool($input["no_parent"]) : true;
			$showparent = $showparent && $man->verifyPath($file->getParent());

			if (!isset($input["only_dirs"]))
				$showparent = $showparent && checkBool($config["filesystem.list_directories"], true);

			// Add parent
			if ($showparent && !isset($input["only_files"])) {
				// Remove files below root
				if ($filter_root_path && getClassName($file) == 'moxiecode_localfileimpl') {
					if (!$man->isChildPath($filter_root_path, $file->getParent()))
						return $files;
				}

				if ($file->getAbsolutePath() != $filter_root_path)
					$result->add("..", $man->encryptPath($file->getParent()), -1, "parent", "", "", "", array());
			}
		} else
			trigger_error("The specified path is not a directory. Probably an incorrect setting for the filesystem.rootpath option.", FATAL);

		return $files;
	}

	/**
	 * Lists files.
	 */
	function _listFiles(&$man, $input) {
		$result = new Moxiecode_ResultSet("name,path,size,type,created,modified,attribs,custom");
		$config = $man->getConfig();
		$files = array();
		$rootNames = $man->_getRootNames();
		$filterRootPath = isset($input["root_path"]) && $input["root_path"] != null ? $man->toAbsPath($man->decryptPath($input["root_path"])) : null;

		if (isset($input["path"]) && $input["path"]) {
			// URL specified
			if (isset($input["url"]) && $input["path"] == '{default}')
				$input["path"] = $man->convertURIToPath($input["url"]);

			if (isset($input['remember_last_path'])) {
				if ($input['remember_last_path'] !== 'auto')
					$remember = checkBool($input['remember_last_path']);
				else
					$remember = checkBool($config['general.remember_last_path']);

				if ($remember) {
					if (isset($_COOKIE["MCManager_". $man->getType() . "_lastPath"]) && $input["path"] == '{default}') {
						$tmpPath = $_COOKIE["MCManager_". $man->getType() . "_lastPath"];

						if ($man->getFSFromPath($tmpPath) == "file" && $tmpPath)
							$input["path"] = $tmpPath;
					} else {
						if ($man->getFSFromPath($input["path"]) == "file")
							setcookie("MCManager_". $man->getType() . "_lastPath", $input["path"], time() + (3600*24*30)); // 30 days
					}
				}
			}

			$input["path"] = $man->toAbsPath($man->decryptPath($input["path"]));

			$result->setHeader("path", $man->encryptPath($input["path"]));
			$result->setHeader("visual_path", checkBool($config['general.user_friendly_paths']) ? $man->toVisualPath($input["path"]) : $man->encryptPath($input["path"]));

			// Move path inside rootpath if it's localfs
			if ($filterRootPath && $man->getFSFromPath($input["path"]) == 'file') {
				if (!$man->isChildPath($filterRootPath, $input["path"]))
					$input["path"] = $filterRootPath;

				$result->setHeader("path", $man->encryptPath($input["path"]));
				$result->setHeader("visual_path", checkBool($config['general.user_friendly_paths']) ? $man->toVisualPath($input["path"], $filterRootPath) : $man->encryptPath($input["path"]));
			}

			// Not valid path use default path
			if ($man->getFSFromPath($input["path"]) == 'file' && !$man->verifyPath($input["path"])) {
				$input["path"] = $config['filesystem.path'];

				$result->setHeader("path", $man->encryptPath($input["path"]));
				$result->setHeader("visual_path", checkBool($config['general.user_friendly_paths']) ? $man->toVisualPath($input["path"]) : $man->encryptPath($input["path"]));
			}

			$file =& $man->getFile($input["path"]);

			$config = $file->getConfig();
			$attribs = ($file->canRead() && checkBool($config["filesystem.readable"]) ? "R" : "-") . ($file->canWrite() && checkBool($config["filesystem.writable"]) ? "W" : "-");
			$result->setHeader("attribs", $attribs);
			$files = $this->_listDefault($man, $file, $input, $result, $filterRootPath);
		} else {
			trigger_error("ListFiles input not valid.", FATAL);
			die();
		}

		if (isset($input["page_size"])) {
			if ($file->getAbsolutePath() != $filterRootPath && $man->verifyPath($file->getParent()))
				$pageSize = $input["page_size"] - 1;
			else
				$pageSize = $input["page_size"];

			$pages = ceil(count($files) / $pageSize);

			// Setup response
			$result->setHeader("pages", $pages > 1 ? $pages : 1);
			$result->setHeader("count", count($files));

			if (!isset($input["page"]))
				$input["page"] = 0;

			// Remove non visible files
			$files = array_slice($files, ($input["page"] * $pageSize), $pageSize);
		}

		// Add directories
		$listFS = $man->getFSFromPath($input["path"]);
		foreach ($files as $file) {
			// Remove files below root
			if ($filterRootPath && $listFS == 'file') {
				if (!$man->isChildPath($filterRootPath, $file->getAbsolutePath()))
					continue;
			}

			// Setup fields
			$custom = array();
			// Attribs: R = Read, W = Write (cut/delete/rename), D = Download, S = Stream/Preview, I = Insert
			$attribs = ($file->canRead() && checkBool($config["filesystem.readable"]) ? "R" : "-") . ($file->canWrite() && checkBool($config["filesystem.writable"]) ? "W" : "-");
			$cdate = date($config['filesystem.datefmt'], $file->getCreationDate());
			$mdate = date($config['filesystem.datefmt'], $file->getLastModified());
			$filePath = $man->encryptPath($file->getAbsolutePath());

			if ($file->isFile())
				$type = getFileExt($file->getName());
			else
				$type = "folder";

			$man->dispatchEvent("onCustomInfo", array(&$file, "list", &$custom));

			// Special treatment of roots
			$name = $file->getName();
			if ($input["path"] == "root:///") {
				foreach ($rootNames as $rootPath => $rootName) {
					if ($file->getAbsolutePath() == $rootPath) {
						$name = $rootName;
						break;
					}
				}
			}

			$result->add(utf8_encode($name), $filePath, $file->isFile() ? $file->getLength() : -1, $type, $cdate, $mdate, $attribs, $custom);
		}

		if (isset($input["config"]))
			$result->setConfig($man->getJSConfig($config, $input["config"]));

		return $result->toArray();
	}

	function _filterFile(&$file, $input) {
		$config = $file->getConfig();

		// Setup file filter
		$fileFilter = new Moxiecode_BasicFileFilter();
		//$fileFilter->setDebugMode(true);
		$fileFilter->setIncludeDirectoryPattern($config['filesystem.include_directory_pattern']);
		$fileFilter->setExcludeDirectoryPattern($config['filesystem.exclude_directory_pattern']);
		$fileFilter->setIncludeFilePattern($config['filesystem.include_file_pattern']);
		$fileFilter->setExcludeFilePattern($config['filesystem.exclude_file_pattern']);
		$fileFilter->setIncludeExtensions($config['filesystem.extensions']);

		if (isset($input["only_dirs"]) && checkBool($input["only_dirs"]))
			$fileFilter->setOnlyDirs(true);

		return ($fileFilter->accept($file) > 0);
	}

	function _createDirs(&$man, $input) {
		$result = new Moxiecode_ResultSet("status,file,message");

		// Get input data
		$path = $man->decryptPath($input['path']);
		$dir =& $man->getFile($path);
		$config = $dir->getConfig();

		if (checkBool($config["general.demo"])) {
			$result->add("DEMO_ERROR", $man->encryptPath($dir->getAbsolutePath()), "{#error.demo}");
			return $result->toArray();
		}

		if (!$man->isToolEnabled("createdir", $config)) {
			trigger_error("{#error.no_access}", FATAL);
			die();
		}

		for ($i=0; isset($input['name' . $i]); $i++) {
			// Get dir info
			$name = $input['name' . $i];

			$template = false;
			if (isset($input['template' . $i]))
				$template = $man->decryptPath($input['template' . $i]);

			// Setup target file
			$file =& $man->getFile($path, $name, MC_IS_DIRECTORY);
			if ($man->verifyFile($file, "createdir") < 0) {
				$result->add("ACCESS_ERROR", $man->encryptPath($file->getAbsolutePath()), $man->getInvalidFileMsg());
				continue;
			}

			// Check write access
			if (!checkBool($config["filesystem.writable"])) {
				$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), "{#error.no_write_access}");
				continue;
			}

			// Setup template dir
			if ($template) {
				$templateFile =& $man->getFile($template, "", MC_IS_DIRECTORY);

				if (!$templateFile->exists()) {
					$result->add("TEMPLATE_ERROR", $man->encryptPath($templateFile->getAbsolutePath()), "{#error.template_missing}");
					continue;
				}

				if ($man->verifyFile($templateFile, "createdir") < 0) {
					$result->add("ACCESS_ERROR", $man->encryptPath($file->getAbsolutePath()), $man->getInvalidFileMsg());
					continue;
				}
			} else
				$templateFile = null;

			// Check if target exists
			if ($file->exists()) {
				$result->add("EXISTS_ERROR", $man->encryptPath($file->getAbsolutePath()), "{#error.folder_exists}");
				continue;
			}

			// Create directory
			if ($templateFile)
				$status = $templateFile->copyTo($file);
			else
				$status = $file->mkdir();

			if ($status)
				$result->add("OK", $man->encryptPath($file->getAbsolutePath()), "{#message.directory_ok}");
			else
				$result->add("FAILED", $man->encryptPath($file->getAbsolutePath()), "{#error.no_write_access}");
		}

		return $result->toArray();
	}

	function _getConfig(&$man, $input) {
		$globalConfig = $man->getConfig();

		if (!isset($input['prefixes']))
			$input["prefixes"] = "*";

		// If debug mode return all
		if (!isset($input['path']) && isset($input['debug']) && checkBool($globalConfig['general.debug']))
			return $man->getConfig();

		if (!isset($input['path'])) {
			trigger_error("{#error.file_not_exists}", FATAL);
			die;
		}

		$file =& $man->getFile($input['path']);

		if (!$file->exists()) {
			trigger_error("{#error.file_not_exists}", FATAL);
			die;
		}

		$config = $file->getConfig();

		// If debug mode return all
		if (isset($input['debug']) && checkBool($globalConfig['general.debug']))
			return $config;

		return $man->getJSConfig($config, $input["prefixes"]);
	}

	/**
	 * Simple keepalive function.
	 */
	function _keepAlive(&$man, $input) {
		$result = new Moxiecode_ResultSet("status,time,message");

		$man->dispatchEvent("onKeepAlive");

		// Return status KEEPALIVE, current time on server and message.
		$result->add("KEEPALIVE", time(), "{#message.keepalive}");

		return $result->toArray();
	}

	/**
	 * Simple loopback function. Used for debugging purposes of the RPC functionality.
	 */
	function _loopBack($input) {
		return $input;
	}

	/**#@-*/
}

// Add plugin to MCManager
$man->registerPlugin("core", new Moxiecode_CorePlugin());
?>