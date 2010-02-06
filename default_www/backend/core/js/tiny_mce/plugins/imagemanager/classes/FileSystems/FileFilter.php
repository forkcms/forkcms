<?php
/**
 * $Id: FileFilter.php 539 2008-10-30 13:16:58Z spocke $
 *
 * @package MCFileManager.filesystems
 * @author Moxiecode
 * @copyright Copyright © 2005, Moxiecode Systems AB, All rights reserved.
 */

/**
 * This class is the base FileFilter class and is to be extended by all custom FileFilter implementations.
 *
 * @package mce.core
 */
class Moxiecode_FileFilter {
	/**
	 * Returns true or false if the file is accepted or not.
	 * 
	 * @param MCE_File $file File to grant or deny.
	 * @return boolean true or false if the file is accepted or not.
	 */
	function accept(&$file) {
		// code here...
	}
}

/**
 * Combines multiple filters into one filter.
 *
 * @package mce.core
 */
class Moxiecode_CombinedFileFilter {
	var $_filters;

	/**
	 * Constructs a new combined filer.
	 */
	function Moxiecode_CombinedFileFilter() {
		$this->_filters = array();
	}

	/**
	 * Adds a new filter to check.
	 *
	 * @param Moxiecode_FileFilter $file_filter Filter to add.
	 */
	function addFilter(&$file_filter) {
		$this->_filters[] = $file_filter;
	}

	/**
	 * Returns true or false if the file is accepted or not.
	 * 
	 * @param MCE_File $file File to grant or deny.
	 * @return boolean true or false if the file is accepted or not.
	 */
	function accept(&$file) {
		for ($i = 0; $i < count($this->_filters); $i++) {
			$state = $this->_filters[$i]->accept($file);

			if ($state < 0)
				return $state;
		}

		return 1;
	}
}

/**
 * DummyFileFiler this filter accepts all files.
 *
 * @package mce.core
 */
class Moxiecode_DummyFileFilter extends Moxiecode_FileFilter {
	/**
	 * Returns true or false if the file is accepted or not.
	 * Note: This dummb method allways returns true.
	 * 
	 * @param MCE_File $file File to grant or deny.
	 * @return boolean true or false if the file is accepted or not.
	 */
	function accept(&$file) {
		return true;
	}
}

// Define reason constants
define('BASIC_FILEFILTER_ACCEPTED', 1);
define('BASIC_FILEFILTER_INVALID_EXTENSION', -1);
define('BASIC_FILEFILTER_INVALID_NAME', -2);

/**
 * Basic file filter, this class handles some common filter problems
 * and is possible to extend if needed.
 *
 * @package mce.core
 */
class Moxiecode_BasicFileFilter extends Moxiecode_FileFilter {
	/**#@+
	 * @access private
	 */

	var $_excludeFolders;
	var $_includeFolders;
	var $_excludeFiles;
	var $_includeFiles;
	var $_includeFilePattern;
	var $_excludeFilePattern;
	var $_includeDirectoryPattern;
	var $_excludeDirectoryPattern;
	var $_filesOnly;
	var $_dirsOnly;
	var $_includeWildcardPattern;
	var $_excludeWildcardPattern;
	var $_extensions;
	var $_maxLevels;
	var $_debug;

    /**#@+
	 * @access public
	 */

	/**
	 * Main constructor.
	 */
	function BasicFileFilter() {
		$this->_debug = false;
		$this->_extensions = "";
	}

	/**
	 * Sets if debug mode is on or off, default off.
	 * 
	 * @param boolean $state if true debug mode is enabled.
	 */
	function setDebugMode($state) {
		$this->_debug = $state;
	}

	/**
	 * Sets if only files are to be accepted in result.
	 * 
	 * @param boolean $files_only True if only files are to be accepted.
	 */
	function setOnlyFiles($files_only) {
		$this->_filesOnly = $files_only;
	}

	/**
	 * Sets if only dirs are to be accepted in result.
	 * 
	 * @param boolean $dirs_only True if only dirs are to be accepted.
	 */
	function setOnlyDirs($dirs_only) {
		$this->_dirsOnly = $dirs_only;
	}

	/**
	 * Sets maximum number of directory levels to accept.
	 * 
	 * @param int $max_levels Maximum number of directory levels to accept.
	 */
	function setMaxLevels($max_levels) {
		$this->_maxLevels = $max_levels;
	}

	/**
	 * Sets a comma separated list of valid file extensions.
	 *
	 * @param String $extensions Comma separated list of valid file extensions.
	 */
	function setIncludeExtensions($extensions) {
		if ($extensions == "*" || $extensions == "")
			return;

		$this->_extensions = explode(',', strtolower($extensions));
	}

	/**
	 * Sets comma separated string list of filenames to exclude.
	 * 
	 * @param String $files separated string list of filenames to exclude.
	 */
	function setExcludeFiles($files) {
		if ($files != "")
			$this->_excludeFiles = split(',', $files);
	}

	/**
	 * Sets comma separated string list of filenames to include.
	 * 
	 * @param String $files separated string list of filenames to include.
	 */
	function setIncludeFiles($files) {
		if ($files != "")
			$this->_includeFiles = split(',', $files);
	}

	/**
	 * Sets comma separated string list of foldernames to exclude.
	 * 
	 * @param String $folders separated string list of foldernames to exclude.
	 */
	function setExcludeFolders($folders) {
		if ($folders != "")
			$this->_excludeFolders = split(',', $folders);
	}

	/**
	 * Sets comma separated string list of foldernames to include.
	 * 
	 * @param String $folders separated string list of foldernames to include.
	 */
	function setIncludeFolders($folders) {
		if ($folders != "")
			$this->_includeFolders = split(',', $folders);
	}

	/**
	 * Sets a regexp pattern that is used to accept files path parts.
	 * 
	 * @param String $pattern regexp pattern that is used to accept files path parts.
	 */
	function setIncludeFilePattern($pattern) {
		$this->_includeFilePattern = $pattern;
	}

	/**
	 * Sets a regexp pattern that is used to deny files path parts.
	 * 
	 * @param String $pattern regexp pattern that is used to deny files path parts.
	 */
	function setExcludeFilePattern($pattern) {
		$this->_excludeFilePattern = $pattern;
	}

	/**
	 * Sets a regexp pattern that is used to accept directory path parts.
	 * 
	 * @param String $pattern regexp pattern that is used to accept directory path parts.
	 */
	function setIncludeDirectoryPattern($pattern) {
		$this->_includeDirectoryPattern = $pattern;
	}

	/**
	 * Sets a regexp pattern that is used to deny directory path parts.
	 * 
	 * @param String $pattern regexp pattern that is used to deny directory path parts.
	 */
	function setExcludeDirectoryPattern($pattern) {
		$this->_excludeDirectoryPattern = $pattern;
	}

	/**
	 * Sets a wildcard pattern that is used to accept files path parts.
	 * 
	 * @param String $pattern wildcard pattern that is used to accept files path parts.
	 */
	function setIncludeWildcardPattern($pattern) {
		if ($pattern != "")
			$this->_includeWildcardPattern = $pattern;
	}

	/**
	 * Sets a wildcard pattern that is used to deny files path parts.
	 * 
	 * @param String $pattern wildcard pattern that is used to deny files path parts.
	 */
	function setExcludeWildcardPattern($pattern) {
		if ($pattern != "")
			$this->_excludeWildcardPattern = $pattern;
	}

	/**
	 * Returns true or false if the file is accepted or not.
	 * 
	 * @param MCE_File $file File to grant or deny.
	 * @return boolean true or false if the file is accepted or not.
	 */
	function accept(&$file) {
		$name = $file->getName();
		$absPath = $file->getAbsolutePath();
		$isFile = $file->isFile();

		// Handle exclude folders
		if (is_array($this->_excludeFolders)) {
			foreach ($this->_excludeFolders as $folder) {
				if (strpos($absPath, $folder) != "") {
					if ($this->_debug)
						debug("File denied \"" . $absPath . "\" by \"excludeFolders\".");

					return BASIC_FILEFILTER_INVALID_NAME;
				}
			}
		}

		// Handle include folders
		if (is_array($this->_includeFolders)) {
			$state = false;

			foreach ($this->_includeFolders as $folder) {
				if (strpos($absPath, $folder) != "") {
					$state = true;
					break;
				}
			}

			if (!$state) {
				if ($this->_debug)
					debug("File \"" . $absPath . "\" denied by \"includeFolders\".");

				return BASIC_FILEFILTER_INVALID_NAME;
			}
		}

		// Handle exclude files
		if (is_array($this->_excludeFiles) && $isFile) {
			foreach ($this->_excludeFiles as $fileName) {
				if ($name == $fileName) {
					if ($this->_debug)
						debug("File \"" . $absPath . "\" denied by \"excludeFiles\".");

					return BASIC_FILEFILTER_INVALID_NAME;
				}
			}
		}

		// Handle include files
		if (is_array($this->_includeFiles) && $isFile) {
			$state = false;

			foreach ($this->_includeFiles as $fileName) {
				if ($name == $fileName) {
					$state = true;
					break;
				}
			}

			if (!$state) {
				if ($this->_debug)
					debug("File \"" . $absPath . "\" denied by \"includeFiles\".");

				return BASIC_FILEFILTER_INVALID_NAME;
			}
		}

		// Handle file patterns
		if ($isFile) {
			if ($this->_dirsOnly) {
				if ($this->_debug)
					debug("File denied \"" . $absPath . "\" by \"dirsOnly\".");

				return BASIC_FILEFILTER_INVALID_NAME;
			}

			// Handle exclude pattern
			if ($this->_excludeFilePattern && preg_match($this->_excludeFilePattern, $name)) {
				if ($this->_debug)
					debug("File \"" . $absPath . "\" denied by \"excludeFilePattern\".");

				return BASIC_FILEFILTER_INVALID_NAME;
			}

			// Handle include pattern
			if ($this->_includeFilePattern && !preg_match($this->_includeFilePattern, $name)) {
				if ($this->_debug)
					debug("File \"" . $absPath . "\" denied by \"includeFilePattern\".");

				return BASIC_FILEFILTER_INVALID_NAME;
			}
		} else {
			if ($this->_filesOnly) {
				if ($this->_debug)
					debug("Dir denied \"" . $absPath . "\" by \"filesOnly\".");

				return BASIC_FILEFILTER_INVALID_NAME;
			}

			// Handle exclude pattern
			if ($this->_excludeDirectoryPattern && preg_match($this->_excludeDirectoryPattern, $name)) {
				if ($this->_debug)
					debug("File \"" . $absPath . "\" denied by \"excludeDirectoryPattern\".");

				return BASIC_FILEFILTER_INVALID_NAME;
			}

			// Handle include pattern
			if ($this->_includeDirectoryPattern && !preg_match($this->_includeDirectoryPattern, $name)) {
				if ($this->_debug)
					debug("File \"" . $absPath . "\" denied by \"includeDirectoryPattern\".");

				return BASIC_FILEFILTER_INVALID_NAME;
			}
		}

		// Handle include wildcard pattern
		if ($this->_includeWildcardPattern && !$this->_fnmatch($this->_includeWildcardPattern, $name)) {
			if ($this->_debug)
				debug("File \"" . $absPath . "\" denied by \"includeWildcardPattern\".");

			return BASIC_FILEFILTER_INVALID_NAME;
		}

		// Handle exclude wildcard pattern
		if ($this->_excludeWildcardPattern && $this->_fnmatch($this->_excludeWildcardPattern, $name)) {
			if ($this->_debug)
				debug("File \"" . $absPath . "\" denied by \"excludeWildcardPattern\".");

			return BASIC_FILEFILTER_INVALID_NAME;
		}

		// Handle file exntetion pattern
		if (is_array($this->_extensions) && $isFile) {
			$ar = explode('.', $absPath);
			$ext = strtolower(array_pop($ar));
			$valid = false;

			foreach ($this->_extensions as $extension) {
				if ($extension == $ext) {
					$valid = true;
					break;
				}
			}

			if (!$valid)
				return BASIC_FILEFILTER_INVALID_EXTENSION;
		}

		return BASIC_FILEFILTER_ACCEPTED;
	}

	function _fnmatch($pattern, $file) {
		return ereg($this->_fnmatch2regexp(strtolower($pattern)), strtolower($file));
	}

	function _fnmatch2regexp($str) {
		$s = "";

		for ($i = 0; $i<strlen($str); $i++) {
			$c = $str{$i};
			if ($c =='?')
				$s .= '.'; // any character
			else if ($c == '*')  
				$s .= '.*'; // 0 or more any characters
			else if ($c == '[' || $c == ']')
				$s .= $c;  // one of characters within []
			else
				$s .= '\\' . $c;
		}

		$s = '^' . $s . '$';

		//trim redundant ^ or $
		//eg ^.*\.txt$ matches exactly the same as \.txt$
		if (substr($s,0,3) == "^.*")
			$s = substr($s, 3);

		if (substr($s,-3,3) == ".*$")
			$s = substr($s, 0, -3);

		return $s;
	}

	/**#@-*/
}
?>