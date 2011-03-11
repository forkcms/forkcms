<?php
/**
 * $Id: ManagerEngine.php 751 2009-10-20 12:05:36Z spocke $
 *
 * @package MCManager
 * @author Moxiecode
 * @copyright Copyright © 2007, Moxiecode Systems AB, All rights reserved.
 */

// Get base path
define('MCMANAGER_ABSPATH', dirname(__FILE__) . "/");

// Import core classes
require_once(MCMANAGER_ABSPATH . "Utils/Logger.class.php");
require_once(MCMANAGER_ABSPATH . "ManagerPlugin.php");
require_once(MCMANAGER_ABSPATH . "Utils/ResultSet.php");
require_once(MCMANAGER_ABSPATH . "Utils/LanguagePack.php");
require_once(MCMANAGER_ABSPATH . "FileSystems/BaseFile.php");
require_once(MCMANAGER_ABSPATH . "FileSystems/FileStream.php");
require_once(MCMANAGER_ABSPATH . "FileSystems/FileFilter.php");
require_once(MCMANAGER_ABSPATH . "FileSystems/FileTreeHandler.php");

/**
 * This class handles the core logic of the MCManager it's responsible for event handeling, configuration management,
 * language packs and plugins.
 *
 * @package MCManager
 */
class Moxiecode_ManagerEngine {
	/**#@+
	 * @access private
	 */

	var $_config;
	var $_plugins;
	var $_prefixes;
	var $_fileSystems;
	var $_rootPaths;
	var $_language;
	var $_type;
	var $_logger;
	var $_langPackPath;

	/**#@+
	 * @access public
	 */

	/**
	 * Main constructor.
	 *
	 * @param String $type Language pack type prefix. Used inorder to load the correct language pack like im or fm.
	 */
	function Moxiecode_ManagerEngine($type) {
		$this->_plugins = array();
		$this->_prefixes = array();
		$this->_rootPaths = array();
		$this->_config = array();
		$this->_type = $type;
	}

	/**
	 * Sets the name/value array of config options. This method will also force some relative config option paths to absolute.
	 *
	 * @param Array $config Name/value array of config options.
	 * @param bool $setup_values True/false if the paths etc of the config should be set to default values or not.
	 */
	function setConfig($config, $setup_values = true) {
		$this->_rootPaths = array();

		if ($setup_values) {
			// Auto add rootpaths and force them absolute in config
			$newRoots = array();
			$roots = explode(';', $config['filesystem.rootpath']);
			foreach ($roots as $root) {
				$rootParts = explode('=', $root);

				// Unnamed root
				if (count($rootParts) == 1) {
					$rootParts[0] = $this->removeTrailingSlash($this->toAbsPath($rootParts[0]));
					$this->addRootPath($rootParts[0]);
				}

				// Named root
				if (count($rootParts) == 2) {
					$rootParts[1] = $this->removeTrailingSlash($this->toAbsPath($rootParts[1]));
					$this->addRootPath($rootParts[1]);
				}

				$newRoots[] = implode('=', $rootParts);
			}

			$config['filesystem.rootpath'] = implode(';', $newRoots);

			// Force absolute path
			if ($config['filesystem.path'] == "")
				$config['filesystem.path'] = $this->_rootPaths[0];
			else
				$config['filesystem.path'] = $this->removeTrailingSlash($this->toAbsPath($config['filesystem.path']));

			// Check if path is within any of the roots
			$found = false;
			foreach ($this->_rootPaths as $root) {
				if ($this->isChildPath($root, $config['filesystem.path'])) {
					$found = true;
					break;
				}
			}

			// Path was not within any of the rootpaths use the first one
			if (!$found)
				$config['filesystem.path'] = $this->_rootPaths[0];

			// Setup absolute wwwroot
			if (isset($config['preview.wwwroot']) && $config['preview.wwwroot'])
				$config['preview.wwwroot'] = $this->toUnixPath($this->toAbsPath($config['preview.wwwroot']));
			else
				$config['preview.wwwroot'] = $this->getSiteRoot();

			// Setup preview.urlprefix
			if ($config["preview.urlprefix"]) {
				if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on")
					$config["preview.urlprefix"] = str_replace("{proto}", "https", $config["preview.urlprefix"]);
				else
					$config["preview.urlprefix"] = str_replace("{proto}", "http", $config["preview.urlprefix"]);

				$config["preview.urlprefix"] = str_replace("{host}", $_SERVER['HTTP_HOST'], $config["preview.urlprefix"]);
				$config["preview.urlprefix"] = str_replace("{port}", $_SERVER['SERVER_PORT'], $config["preview.urlprefix"]);
			}
		}

		$this->_config =& $config;
	}

	function getType() {
		return $this->_type;
	}

	function setLangPackPath($path) {
		$this->_langPackPath = $path;
	}

	function getLangPackPath() {
		return $this->_langPackPath;
	}

	/**
	 * Returns a LanguagePack instance of the current language pack.
	 *
	 * @return LanguagePack Instance of the current language pack.
	 */
	function &getLangPack() {
		if (!$this->_language) {
			$config = $this->getConfig();
			$this->_language = new Moxiecode_LanguagePack();
			$this->_language->load($this->toAbsPath("language/" . $this->_langPackPath . "/" . $config['general.language'] . ".xml"));
		}

		return $this->_language;
	}

	/**
	 * Returns a lang item by group and key.
	 *
	 * @param string $group Language group to look in.
	 * @param string $item Item to get inside group.
	 * @param Array $replace Name/Value array of variables to replace in language pack.
	 * @return string Language pack string loaded from XML file.
	 */
	function getLangItem($group, $item, $replace = array()) {
		$pack =& $this->getLangPack();

		$string = $pack->get($group, $item);

		foreach ($replace as $key => $val)
			$string = str_replace("{". $key ."}", $val, $string);

		return $string;
	}

	/**
	 * Returns an array of plugin paths to be loaded/required/includes.
	 *
	 * @return Array Array of file paths to load.
	 */
	function getPluginPaths() {
		$config = $this->getConfig();
		$plugins = array();

		if (isset($config["general.plugins"]) && $config["general.plugins"])
			$plugins = preg_split("/,/i", $config["general.plugins"]);

		$out = array();
		foreach($plugins as $plugin) {
			if (!isset($this->_plugins[strtolower($plugin)])) {
				// Check for single file plugins
				if (file_exists(MCMANAGER_ABSPATH . "../plugins/" . $plugin . ".php"))
					$out[] = "plugins/" . $plugin . ".php";
				else
					$out[] = "plugins/" . $plugin . "/". $plugin . ".php";
			}
		}

		// Check the string first
		if ($config["authenticator"] == "")
			$config["authenticator"] = "BaseAuthenticator";

		$authenticators = array();
		// Check string for delimiter, preg_split returns php warning if delimiter is not found!
		if (strpos($config["authenticator"], "+") || strpos($config["authenticator"], "|"))
			$authenticators = preg_split("/\+|\|/i", $config["authenticator"], -1, PREG_SPLIT_NO_EMPTY);

		if (count($authenticators) != 0) {
			foreach ($authenticators as $auth) {
				// Check for single file plugins
				if (!isset($this->_plugins[strtolower($auth)])) {
					if (file_exists(MCMANAGER_ABSPATH . "../plugins/" . $auth . ".php"))
						$out[] = "plugins/" . $auth . ".php";
					else
						$out[] = "plugins/" . $auth . "/". $auth . ".php";
				}
			}
		} else {
			if (!isset($this->_plugins[strtolower($config["authenticator"])])) {
				if (file_exists(MCMANAGER_ABSPATH . "Authenticators/" . $config["authenticator"] . ".php"))
					$out[] = "classes/Authenticators/" . $config["authenticator"] . ".php";
				else if (file_exists(MCMANAGER_ABSPATH . "../plugins/" . $config["authenticator"] . ".php"))
					$out[] = "plugins/" . $config["authenticator"] . ".php";
				else
					$out[] = "plugins/" . $config["authenticator"] . "/" . $config["authenticator"] . ".php";
			}
		}

		// Check so that they all exists
		foreach ($out as $path) {
			if (!file_exists(MCMANAGER_ABSPATH . "../" . $path))
				trigger_error("Plugin could not be found: " . $path, FATAL);
		}

		return $out;
	}

	/**
	 * Adds a path to the list of root paths.
	 *
	 * @param String $path Path to add must be a absolute path.
	 */
	function addRootPath($path) {
		$this->_rootPaths[] = $path;
	}

	/**
	 * Returns an array of root paths.
	 *
	 * @return Array Root paths.
	 */
	function getRootPaths() {
		return $this->_rootPaths;
	}

	/**
	 * Returns a plugin by id/name.
	 *
	 * @param String $name Plugin id/name.
	 * @return MCManagerPlugin instance object.
	 */
	function getPlugin($name) {
		return isset($this->_plugins[$name]) ? $this->_plugins[$name] : null;
	}

	/**
	 * Returns a true/false check for plugin.
	 *
	 * @param String $name Plugin id/name.
	 * @return Bool true/false
	 */
	function hasPlugin($name) {
		return isset($this->_plugins[$name]);
	}

	/**
	 * Returns a name/value array of plugins.
	 *
	 * @return Array name/value array of MCManagerPlugin instances.
	 */
	function getPlugins() {
		return $this->_plugins;
	}

	/**
	 * Registers a plugin by id/name.
	 *
	 * @param $name Id/name to register plugin by.
	 * @param $plugin Plugin instance to register/add to list.
	 *
	 * @return SmallMCEPlugin The added plugin instance.
	 */
	function &registerPlugin($name, &$plugin, $prefix = false) {
		$name = strtolower($name);
		$this->_plugins[$name] =& $plugin;

		if ($prefix != false)
			$this->_prefixes[$name] = $prefix;

		return $plugin;
	}

	/**
	 * Returns the MCManager config as name/value array.
	 *
	 * @return Array MCManager config as name/value array.
	 */
	function &getConfig() {
		return $this->_config;
	}

	/**
	 * Returns the a config item by name.
	 *
	 * @param string $key Config item key to retrive.
	 * @param string $def Default value to return.
	 * @return mixed config item by name.
	 */
	function getConfigItem($key, $def = false) {
		return isset($this->_config[$key]) ? $this->_config[$key] : $def;
	}

	/**
	 * Returns a merged JS config. It will only export configured items controlled by the allow_export suffix.
	 *
	 * @return Array Name/Value array of JS config options.
	 */
	function &getJSConfig($config = false, $prefixes = '*') {
		$encrypted = array("filesystem.path", "filesystem.rootpath", "filesystem.directory_templates");
		$jsConfig = array();
		$prefixes = explode(",", $prefixes);

		if (!$config)
			$config = $this->getConfig();

		foreach ($config as $name => $value) {
			$pos = strpos($name, ".allow_export");

			if ($pos > 0) {
				$names = explode(",", $value);
				$prefix = substr($name, 0, $pos);

				if (in_array("*", $prefixes) || in_array($prefix, $prefixes)) {
					foreach ($names as $key) {
						$key = $prefix . "." . $key ;

						// Encrypt some paths
						if (in_array($key, $encrypted))
							$jsConfig[$key] = "" . $this->encryptPath($config[$key]);
						else
							$jsConfig[$key] = "" . is_bool($config[$key]) ? ($config[$key] ? "true" : "false") : $config[$key];
					}
				}
			}
		}

		return $jsConfig;
	}

	/**
	 * Encrypts the specified path so that it doesn't contain full paths on the client side of the application.
	 *
	 * @param string $path Path to encrypt.
	 * @return string Encrypted short path.
	 */
	function encryptPath($path) {
		$config = $this->getConfig();

		if (checkBool($config['general.encrypt_paths'])) {
			$count = 0;

			foreach ($this->_rootPaths as $rootPath) {
				// Needs encryption?
				if ($rootPath != "/")
					$path = str_replace($rootPath, "{" . $count++ . "}", $path);
			}
		}

		return utf8_encode($path);
	}

	/**
	 * Decrypts the specified path from a non absolute path to a absolute path.
	 *
	 * @param string $path Path to decrypt.
	 * @return string Decrypted absolute path.
	 */
	function decryptPath($path) {
		if (!$path)
			return "";

		$count = 0;
		$path = $this->toUnixPath($path);

		// Is relative path
		if (!(strpos($path, '/') === 0 || strpos($path, ':') !== false || strpos($path, '{') !== false))
			$path = realpath(dirname(__FILE__) . '/../' . $path);

		foreach ($this->_rootPaths as $rootPath)
			$path = str_replace("{" . $count++ . "}", $rootPath, $path);

		$path = str_replace("{default}", $this->_config["filesystem.path"], $path);

		return $path;
	}

	/**
	 * isAuthenticated checks against the configuration and sends events to auth plugins.
	 * This method will also call the onBeforeInit, onInit and onAfterInit methods if the user was authenticated.
	 *
	 * @return bool Returns true if authenticated, false if not.
	 */
	function isAuthenticated() {
		$config = $this->getConfig();

		$authenticators = strtolower($config["authenticator"]);

		// Check the string first
		if ($authenticators != "" && $authenticators != "BaseAuthenticator") {
			if (strpos($authenticators, "|") && strpos($authenticators, "+"))
				trigger_error("You can not use both + and | at the same time for adding authenticators.", FATAL);

			$pass = false;

			// Check for AND authenticators
			if (strpos($authenticators, "+")) {
				$authArray = preg_split("/\+/i", $authenticators, -1, PREG_SPLIT_NO_EMPTY);

				if (!$authArray)
					trigger_error("No Authenticator could be used.", FATAL);

				// Verify that all authenticators exists
				foreach($authArray as $auth) {
					if (!$this->hasPlugin($auth))
						trigger_error("Authenticator \"". htmlentities($auth) ."\" was not found.", FATAL);
				}

				// Default to true
				$pass = true;

				// Send AND event
				foreach ($authArray as $auth) {
					$plugin = $this->getPlugin($auth);
					if ($pass && !$plugin->onAuthenticate($this))
						$pass = false;
				}

			// Check for OR authentocator string
			} else if (strpos($authenticators, "|")) {
				$authArray = preg_split("/\|/i", $authenticators, -1, PREG_SPLIT_NO_EMPTY);

				if (!$authArray)
					trigger_error("No Authenticator could be used.", FATAL);

				// Verify that all authenticators exists
				foreach ($authArray as $auth) {
					if (!$this->hasPlugin($auth))
						trigger_error("Authenticator \"". htmlentities($auth) ."\" was not found.", FATAL);
				}

				// Default to false

				$pass = false;
				// Send OR event
				foreach ($authArray as $auth) {
					$plugin = $this->getPlugin($auth);
					if ($plugin->onAuthenticate($this))
						$pass = true;
				}

			} else {
				$plugin = $this->getPlugin($authenticators);

				if ($plugin->onAuthenticate($this))
					$pass = true;
			}
		} else
			$pass = true;

		// Is authenticated, call onInit
		if ($pass)
			$this->dispatchEvent("onInit");

		// Set config again to update rootpaths etc
		$this->setConfig($this->_config);

		return $pass;
	}

	/**
	 * Dispatches a event to all registered plugins. This method will loop through all plugins and call the specific event if this
	 * event method returns false the chain will be terminated.
	 *
	 * @param String $event Event name to be dispatched for example onAjaxCommand.
	 * @param Array $args Optional array with arguments.
	 * @return Bool Returns true of a plugin returned true, false if not.
	 */
	function dispatchEvent($event, $args = false) {
		// Setup event arguments
		$keys = array_keys($this->_plugins);

		for ($i=0; $i<count($keys); $i++) {
			$plugin =& $this->_plugins[$keys[$i]];

			// Valid prefix
			if (isset($this->_prefixes[$keys[$i]])) {
				if ($this->_type != $this->_prefixes[$keys[$i]])
					continue;
			}

			switch ($event) {
				case "onAuthenticate":
					if (!$plugin->onAuthenticate($this))
						return false;

					break;

				case "onInit":
					if (!$plugin->onInit($this))
						return false;

					break;

				case "onPreInit":
					if (!$plugin->onPreInit($this, $args[0]))
						return false;

					break;

				case "onLogin":
					if (!$plugin->onLogin($this))
						return false;

					break;

				case "onLogout":
					if (!$plugin->onLogout($this))
						return false;

					break;

				case "onBeforeFileAction":
					if (!isset($args[2]))
						$args[2] = null;

					if (!$plugin->onBeforeFileAction($this, $args[0], $args[1], $args[2]))
						return false;

					break;

				case "onFileAction":
					if (!isset($args[2]))
						$args[2] = null;

					if (!$plugin->onFileAction($this, $args[0], $args[1], $args[2]))
						return false;

					break;

				case "onBeforeRPC":
					if (!$plugin->onBeforeRPC($this, $args[0], $args[1]))
						return false;

					break;

				case "onBeforeStream":
					if (!$plugin->onBeforeStream($this, $args[0], $args[1]))
						return false;

					break;

				case "onStream":
					if (!$plugin->onStream($this, $args[0], $args[1]))
						return false;

					break;

				case "onAfterStream":
					if (!$plugin->onAfterStream($this, $args[0], $args[1]))
						return false;

					break;

				case "onBeforeUpload":
					if (!$plugin->onBeforeUpload($this, $args[0], $args[1]))
						return false;

					break;

				case "onAfterUpload":
					if (!$plugin->onAfterUpload($this, $args[0], $args[1]))
						return false;

					break;

				case "onCustomInfo":
					if (!$plugin->onCustomInfo($this, $args[0], $args[1], $args[2]))
						return false;

					break;

				case "onListFiles":
					if (!$plugin->onListFiles($this, $args[0], $args[1]))
						return false;

					break;

				case "onInsertFile":
					if (!$plugin->onInsertFile($this, $args[0]))
						return false;

					break;

				case "onRequestResources":
					if (!$plugin->onRequestResources($this, $args[0], $args[1], $args[2], $args[3], $args[4]))
						return false;

					break;
			}
		}

		return true;
	}

	/**
	 * Executes a event in all registered plugins if a plugin returns a object or array the execution chain will be
	 * terminated.
	 *
	 * @param String $event Event name to be dispatched for example onAjaxCommand.
	 * @param Array $args Optional array with arguments.
	 * @return Bool Returns true of a plugin returned true, false if not.
	 */
	function executeEvent($event, $args=false) {
		// Setup event arguments
		$keys = array_keys($this->_plugins);

		for ($i=0; $i<count($keys); $i++) {
			$plugin =& $this->_plugins[$keys[$i]];

			// Valid prefix
			if (isset($this->_prefixes[$keys[$i]])) {
				if ($this->_type != $this->_prefixes[$keys[$i]])
					continue;
			}

			switch ($event) {
				case "onRPC":
					$result =& $plugin->onRPC($this, $args[0], $args[1]);

					if (!is_null($result))
						return $result;

					break;

				case "onUpload":
					$result =& $plugin->onUpload($this, $args[0], $args[1]);

					if (!is_null($result))
						return $result;

					break;
			}
		}

		return null;
	}

	function getInvalidFileMsg() {
		return $this->_invalidFileMsg;
	}

	/**
	 * Returns the wwwroot if it fails it will trigger a fatal error.
	 *
	 * @return String wwwroot or null string if it was impossible to get.
	 */
	function getSiteRoot() {
		// Check config
		if (isset($this->_config['preview.wwwroot']) && $this->_config['preview.wwwroot'])
			return $this->toUnixPath(realpath($this->_config['preview.wwwroot']));

		// Try script file
		if (isset($_SERVER["SCRIPT_NAME"]) && isset($_SERVER["SCRIPT_FILENAME"])) {
			$path = str_replace($this->toUnixPath($_SERVER["SCRIPT_NAME"]), "", $this->toUnixPath($_SERVER["SCRIPT_FILENAME"]));

			if (is_dir($path))
				return $this->toUnixPath(realpath($path));
		}

		// If all else fails, try this.
		if (isset($_SERVER["SCRIPT_NAME"]) && isset($_SERVER["PATH_TRANSLATED"])) {
			$path = str_replace($this->toUnixPath($_SERVER["SCRIPT_NAME"]), "", str_replace("//", "/", $this->toUnixPath($_SERVER["PATH_TRANSLATED"])));

			if (is_dir($path))
				return $this->toUnixPath(realpath($path));
		}

		// Check document root
		if (isset($_SERVER['DOCUMENT_ROOT']))
			return $this->toUnixPath(realpath($_SERVER['DOCUMENT_ROOT']));

		trigger_error("Could not resolve WWWROOT path, please set an absolute path in preview.wwwroot config option. Check the Wiki documentation for details.", FATAL);

		return null;
	}

	/**
	 * Returns a absolute file system path of a absolute URI path for example /mydir/myfile.htm
	 * will be resolved to /www/mywwwroot/mydir/myfile.htm.
	 *
	 * @param String $uri Absolute URI path for example /mydir/myfile.htm
	 * @param String $root Option site root to use.
	 * @return String Absolute file system path or empty string on failure.
	 */
	function resolveURI($uri, $root = false) {
		// Use default root if not specified
		if (!$root)
			$root = $this->getSiteRoot();

		return realpath($root . $uri);
	}

	/**
	 * Returns a site absolute path from a absolute file system path for example /www/mywwwroot/mydir/myfile.htm
	 * will be converted to /mydir/myfile.htm.
	 *
	 * @param String $abs_path Absolute path for example /mydir/myfile.htm
	 * @return String Site absolute path (URI) or empty string on failure.
	 */
	function convertPathToURI($abs_path, $root = false) {
		$log =& $this->getLogger();

		// No root defined use specified root
		if (!$root)
			$root = $this->getSiteRoot();

		if (!$root) {
			trigger_error("Could not resolve WWWROOT path, please set an absolute path in preview.wwwroot config option.", FATAL);
			die();
		}

		if ($root == "/") {
			if ($log && $log->isDebugEnabled())
				$log->info("ConvertPathToURI: SiteRoot=" . $root . ", Path: " . $abs_path . " -> URI: " . $abs_path);

			return $abs_path;
		}

		$uri = substr($abs_path, strlen($root));

		if ($log && $log->isDebugEnabled())
			$log->info("ConvertPathToURI: SiteRoot=" . $root . ", Path: " . $abs_path . " -> URI: " . $uri);

		return $uri;
	}

	/**
	 * Converts a URI such as /somedir/somefile.gif to /system/path/somedir/somefile.gif
	 *
	 * @param String $uri URI to convert to path.
	 * @param String $root Optional site root to use.
	 * @return String Path out of URI.
	 */
	function convertURIToPath($uri, $root = false) {
		$log =& $this->getLogger();

		if (!$root)
			$root = $this->getSiteRoot();

		if ($log && $log->isDebugEnabled())
			$log->info("ConvertURIToPath: SiteRoot=" . $root . ", URI: " . $uri . " -> Path: " . $this->removeTrailingSlash($root) . $uri);

		return $this->removeTrailingSlash($root) . $uri;
	}

	/**
	 * Converts an path into a visualy presentatble path. So that special folder names
	 * gets translated and root paths gets replaced with their names.
	 *
	 * @param String $path Path to convert into a visual path.
	 * @return String Visual path based on input path.
	 */
	function toVisualPath($path, $root = false) {
		$fs = "file";

		// Parse out FS
		if (preg_match('/([a-z]+):\/\/(.+)/', $path, $matches)) {
			$fs = $matches[1];
			$path = $matches[2];
		}

		$path = $this->decryptPath($path);

		// Speficied root
		if ($root) {
			$pos = strpos($path, $root);

			if ($pos === 0)
				$path = substr($path, strlen($root));

			if ($path == "")
				$path = "/";

			// Re-attach fs
			if ($fs != "file")
				$path = $fs . "://" . $path;

			return $this->encryptPath($path);
		}

		// Use config roots
		$rootNames = $this->_getRootNames();
		foreach ($rootNames as $rootPath => $name) {
			$pos = strpos($path, $rootPath);

			if ($pos === 0) {
				$path = substr($path, strlen($rootPath));

				if ($name == "/")
					$name = "";

				$path = "/" . $name . $path;
			}
		}

		if (!$path)
			$path = "/";

		// Re-attach fs
		if ($fs != "file")
			$path = $fs . "://" . $path;

		return $this->encryptPath($path);
	}

	/**
	 * Verifies that a path is within the parent path.
	 *
	 * @param String $parent_path Parent path that must contain the path.
	 * @param String $path Path that must contained the parent path.
	 * @return Bool true if it's valid, false if it's not. 
	 */
	function isChildPath($parent_path, $path) {
		return strpos(strtolower($path), strtolower($parent_path)) === 0;
	}

	/**
	 * Checks if a specific tool is enabled or not.
	 *
	 * @param string $tool Tool to check for.
	 * @param Array $config Name/Value config array to check tool against.
	 * @return bool true/false if the tool is enabled or not.
	 */
	function isToolEnabled($tool, $config = false) {
		if (!$config)
			$config = $this->getConfig();

		$ar = explode(',', $config['general.disabled_tools']);
		if (in_array($tool, $ar))
			return false;

		$ar = explode(',', $config['general.tools']);
		if (in_array($tool, $ar))
			return true;

		return false;
	}

	/**
	 * Verifies that the specified path is within valid root paths.
	 *
	 * @param String $path Path to verify.
	 * @return Bool true if the path is valid, false if it's invalid.
	 */
	function verifyPath($path) {
		$fs = "file";
		$valid = false;

		// Parse out FS
		if (preg_match('/([a-z]+):\/\/(.+)/', $path, $matches)) {
			$fs = $matches[1];
			$path = $matches[2];
		}

		// Filesystem wasn't found
		if (!isset($this->_fileSystems[$fs])) {
			trigger_error($this->getLangItem("error", "no_filesystem", array("path" => $path)), FATAL);
			die();
		}

		$path = $this->decryptPath($path);

		// /../ is never valid
		if (indexOf($this->addTrailingSlash($path), "/../") != -1)
			return false;

		if ($fs != 'file')
			return true;

		foreach ($this->_rootPaths as $rootPath) {
			if ($this->isChildPath($rootPath, $path))
				$valid = true;
		}

		return $valid;
	}

	/**
	 * Returns the file system for a path for file if it couldn't be extracted.
	 *
	 * @param string $path Path to get FS from.
	 * @return string Filesystem for path.
	 */
	function getFSFromPath($path) {
		$fs = "file";

		// Parse out FS
		if (preg_match('/([a-z]+):\/\/(.+)/', $path, $matches))
			$fs = $matches[1];

		return $fs;
	}

	/**
	 * Verifies that the specified file is valid agains the filters specified in config.
	 *
	 * @param String $path Path to verify.
	 * @param String $action Action to get config options by.
	 * @param Array $config Name/Value array of config options.
	 * @return int Reason why it was denied.
	 */
	function verifyFile($file, $action = false, $config = false) {
		$config = $config ? $config : $file->getConfig();

		// Verify filesystem config
		$fileFilter = new Moxiecode_BasicFileFilter();
		$fileFilter->setIncludeDirectoryPattern($config['filesystem.include_directory_pattern']);
		$fileFilter->setExcludeDirectoryPattern($config['filesystem.exclude_directory_pattern']);
		$fileFilter->setIncludeFilePattern($config['filesystem.include_file_pattern']);
		$fileFilter->setExcludeFilePattern($config['filesystem.exclude_file_pattern']);
		$fileFilter->setIncludeExtensions($config['filesystem.extensions']);

		$this->_invalidFileMsg = "{#error.invalid_filename}";

		$status = $fileFilter->accept($file);

		if ($status != BASIC_FILEFILTER_ACCEPTED) {
			if ($status == BASIC_FILEFILTER_INVALID_NAME) {
				if ($file->isFile() && isset($config['filesystem.invalid_file_name_msg']))
					$this->_invalidFileMsg = $config['filesystem.invalid_file_name_msg'];
				else if (!$file->isFile() && isset($config['filesystem.invalid_directory_name_msg']))
					$this->_invalidFileMsg = $config['filesystem.invalid_directory_name_msg'];

				if (!$this->_invalidFileMsg)
					$this->_invalidFileMsg = "{#error.invalid_filename}";
			}

			return $status;
		}

		// Verify action specific config
		$fileFilter = new Moxiecode_BasicFileFilter();

		if ($action) {
			if (isset($config[$action . '.include_directory_pattern']))
				$fileFilter->setIncludeDirectoryPattern($config[$action . '.include_directory_pattern']);

			if (isset($config[$action . '.exclude_directory_pattern']))
				$fileFilter->setExcludeDirectoryPattern($config[$action . '.exclude_directory_pattern']);

			if (isset($config[$action . '.include_file_pattern']))
				$fileFilter->setIncludeFilePattern($config[$action . '.include_file_pattern']);

			if (isset($config[$action . '.exclude_file_pattern']))
				$fileFilter->setExcludeFilePattern($config[$action . '.exclude_file_pattern']);

			if (isset($config[$action . '.extensions']))
				$fileFilter->setIncludeExtensions($config[$action . '.extensions']);
		} else
			return BASIC_FILEFILTER_ACCEPTED;

		$status = $fileFilter->accept($file);

		if ($status != BASIC_FILEFILTER_ACCEPTED) {
			if ($status == BASIC_FILEFILTER_INVALID_NAME) {
				$this->_invalidFileMsg = "{#error.invalid_filename}";

				if ($file->isFile()) {
					if (isset($config[$action . '.invalid_file_name_msg']))
						$this->_invalidFileMsg = $config[$action . '.invalid_file_name_msg'];
				} else {
					if (isset($config[$action . '.invalid_directory_name_msg']))
						$this->_invalidFileMsg = $config[$action . '.invalid_directory_name_msg'];
				}
			}

			return $status;
		}

		return BASIC_FILEFILTER_ACCEPTED;
	}

	/**
	 * Returns a file object represtentation of a file path this
	 * will also do security checks agains the list of valid paths
	 * so that file IO can't be done outside the valid paths.
	 *
	 * @param String $path Path to return as File object.
	 * @param String $file_name Optional file name.
	 * @param String $type Optional file type.
	 * @return File File object representation of a file.
	 */
	function &getFile($path, $file_name = "", $type = MC_IS_FILE) {
		$path = utf8_decode($path);
		$file_name = utf8_decode($file_name);
		$fs = 'file';
		$matches = array();
		$oldpath = $path;

		// Parse out FS
		if (preg_match('/([a-z]+):\/\/(.+)/', $path, $matches)) {
			$fs = $matches[1];
			$path = $matches[2];
		}

		// Filesystem wasn't found
		if (!isset($this->_fileSystems[$fs])) {
			trigger_error($this->getLangItem("error", "no_filesystem", array("path" => $path)), FATAL);
			die();
		}

		$path = $this->decryptPath($path);
		$path = $this->removeTrailingSlash($this->toUnixPath($path));

		// Verfiy path if no file was returned
		if ($fs == 'file' && !$this->verifyPath($path)) {
			$log =& $this->getLogger();
			if ($log && $log->isDebugEnabled())
				$log->debug("Could not access path: " . $path);

			trigger_error("{#error.no_access}", FATAL);
			die();
		}

		// Validate file name
		if ($fs == 'file' && $file_name) {
			if (preg_match('/[\\\\\\/:]+/', $file_name, $matches)) {
				$log =& $this->getLogger();
				if ($log && $log->isDebugEnabled())
					$log->debug("Could not access path: " . $path);

				trigger_error("{#error.no_access}", FATAL);
				die();
			}
		}

		// Get file instance
		$file = new $this->_fileSystems[$fs]($this, $path, $file_name, $type);

		// Verfiy path if no file was returned
		if ($fs == 'file' && !$this->verifyPath($file->getAbsolutePath())) {
			$log =& $this->getLogger();
			if ($log && $log->isDebugEnabled())
				$log->debug("Could not access path: " . $path);

			trigger_error("{#error.no_access}", FATAL);
			die();
		}

		return $file;
	}

	/**
	 * Converts a relative path to absolute path.
	 *
	 * @param string $path Path to convert to absolute.
	 * @param string $basepath Optional base path default to ../.
	 */
	function toAbsPath($path, $basepath = false) {
		$path = $this->toUnixPath($path);

		if (!$basepath)
			$basepath = dirname(__FILE__) . "/../";

		// Is absolute unix or windows
		if (substr($path, 0, 1) == '/' || strpos($path, ":") !== false) {
			// Resolve symlinks
			$tmp = realpath($path);

			if ($tmp)
				$path = $tmp;

			return $this->toUnixPath($path);
		}

		$path = $this->toUnixPath($this->addTrailingSlash($this->toUnixPath(realpath($basepath))) . $path);

		// Local FS and exists remove any ../../
		if (strpos($path, "://") === false && file_exists($path))
			$path = $this->toUnixPath(realpath($path));

		return $path;
	}

	/**
	 * Converts a Unix path to OS specific path.
	 *
	 * @param String $path Unix path to convert.
	 */
	function toOSPath($path) {
		return str_replace("/", DIRECTORY_SEPARATOR, $path);
	}

	/**
	 * Converts a OS specific path to Unix path.
	 *
	 * @param String $path OS path to convert to Unix style.
	 */
	function toUnixPath($path) {
		return str_replace(DIRECTORY_SEPARATOR, "/", $path);
	}

	/**
	 * Adds a trailing slash to a path.
	 *
	 * @param String path Path to add trailing slash on.
	 * @return String New path with trailing slash.
	 */
	function addTrailingSlash($path) {
		if (strlen($path) > 0 && $path[strlen($path)-1] != '/')
			$path .= '/';

		return $path;
	}

	/**
	 * Removes the trailing slash from a path.
	 *
	 * @param String path Path to remove trailing slash from.
	 * @return String New path without trailing slash.
	 */
	function removeTrailingSlash($path) {
		// Is root
		if ($path == "/")
			return $path;

		if ($path == "")
			return $path;

		if ($path[strlen($path)-1] == '/')
			$path = substr($path, 0, strlen($path)-1);

		return $path;
	}

	/**
	 * Adds a new file system bu name.
	 *
	 * @param String $protocol File protocol like zip/ftp etc.
	 * @param String $file_system Name of class to create instances by.
	 */
	function registerFileSystem($protocol, $file_system) {
		$this->_fileSystems[$protocol] = $file_system;
	}

	/**
	 * Returns a logger instance.
	 *
	 * @return Logger New logger instance.
	 */
	function &getLogger() {
		if (!$this->_logger) {
			$log = new Moxiecode_Logger();

			$null = null; // PHP why!!! Other languages can return null
			if (!checkBool($this->getConfigItem("log.enabled")))
				return $null;

			// Set logger options
			$log->setLevel($this->getConfigItem("log.level", "fatal"));
			$log->setPath($this->toAbsPath($this->getConfigItem("log.path", "logs")));
			$log->setFileName($this->getConfigItem("log.filename", "{level}.log"));
			$log->setFormat($this->getConfigItem("log.format", "[{time}] [{level}] {message}"));
			$log->setMaxSize($this->getConfigItem("log.max_size", "100k"));
			$log->setMaxFiles($this->getConfigItem("log.max_files", "10"));

			$this->_logger = $log;
		}

		return $this->_logger;
	}

	// * * * * * * * Private methods

	function _getRootNames() {
		$config = $this->getConfig();
		$output = array();

		$roots = explode(';', $config['filesystem.rootpath']);
		foreach ($roots as $root) {
			$rootParts = explode('=', $root);

			if (count($rootParts) > 1)
				$output[$rootParts[1]] = $rootParts[0];
			else {
				$output[$rootParts[0]] = basename($root);

				// If it's root
				if ($output[$rootParts[0]] == "")
					$output[$rootParts[0]] = "/";
			}
		}

		return $output;
	}

	/**#@-*/
}

?>