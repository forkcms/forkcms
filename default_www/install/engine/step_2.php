<?php

/**
 * Step 2 of the Fork installer
 *
 * @package		install
 * @subpackage	installer
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @author		Matthias Mullie <matthias@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@dieterve.be>
 * @since		2.0
 */
class InstallerStep2 extends InstallerStep
{
	/**
	 * Check if a specific requirement is satisfied
	 *
	 * @return	boolean
	 * @param	string $variable				The "name" of the check.
	 * @param	bool $requirement				The result of the check.
	 * @param	array[optional] $variables		An array that holds all the variables.
	 */
	public static function checkRequirement($variable, $requirement, array &$variables = null)
	{
		// requirement satisfied
		if($requirement)
		{
			$variables[$variable] = 'ok';
			$variables[$variable . 'Status'] = 'ok';
			return true;
		}

		// requirement not satisfied
		else
		{
			$variables[$variable] = 'nok';
			$variables[$variable . 'Status'] = 'not ok';
			return false;
		}
	}


	/**
	 * Checks the requirements
	 *
	 * @return	bool
	 * @param	array[optional] $variables		An array that holds all the variables.
	 */
	public static function checkRequirements(array &$variables = null)
	{
		// define step
		$step = (isset($_GET['step']) && in_array($_GET['step'], array('1', '2', '3', '4', '5', '6', '7'))) ? (int) $_GET['step'] : 1;

		// define constants
		if(!defined('PATH_WWW') && !defined('PATH_LIBRARY')) self::defineConstants($step);

		/*
		 * At first we're going to check to see if the PHP version meets the minimum requirements
		 * for Fork CMS.
		 */

		// fetch the PHP version.
		$version = (int) str_replace('.', '', PHP_VERSION);

		// we require at least 5.2.x
		self::checkRequirement('phpVersion', $version >= 520, $variables);

		/*
		 * A couple extensions need to be loaded in order to be able to use Fork CMS. Without these
		 * extensions, we can't guarantee that everything will work.
		 */

		// check for cURL extension
		self::checkRequirement('extensionCURL', extension_loaded('curl'), $variables);

		// check for libxml extension
		self::checkRequirement('extensionLibXML', extension_loaded('libxml'), $variables);

		// check for DOM extension
		self::checkRequirement('extensionDOM', extension_loaded('dom'), $variables);

		// check for SimpleXML extension
		self::checkRequirement('extensionSimpleXML', extension_loaded('SimpleXML'), $variables);

		// check for SPL extension
		self::checkRequirement('extensionSPL', extension_loaded('SPL'), $variables);

		// check for mbstring extension
		self::checkRequirement('extensionMBString', extension_loaded('mbstring'), $variables);

		// check for iconv extension
		self::checkRequirement('extensionIconv', extension_loaded('iconv'), $variables);

		// check for gd extension and correct version
		self::checkRequirement('extensionGD2', extension_loaded('gd') && function_exists('gd_info'), $variables);

		// check for PDO extension
		if(extension_loaded('PDO'))
		{
			// general PDO
			$variables['extensionPDO'] = 'ok';
			$variables['extensionPDOStatus'] = 'ok';

			// check for mysql driver
			if(in_array('mysql', PDO::getAvailableDrivers()))
			{
				$variables['extensionPDOMySQL'] = 'ok';
				$variables['extensionPDOMySQLStatus'] = 'ok';
			}

			// mysql driver not found
			else
			{
				$variables['extensionPDOMySQL'] = 'nok';
				$variables['extensionPDOMySQLStatus'] = 'nok';
			}
		}

		// PDO extension not found
		else
		{
			$variables['extensionPDO'] = 'nok';
			$variables['extensionPDOStatus'] = 'not ok';
		}

		/*
		 * A couple of php.ini settings should be configured in a specific way to make sure that
		 * they don't intervene with Fork CMS.
		 */

		// check for safe mode
		// self::checkRequirement('settingsSafeMode', ini_get('safe_mode') == '', $variables);

		// check for open basedir
		// self::checkRequirement('settingsOpenBasedir', ini_get('open_basedir') == '', $variables);

		/*
		 * Make sure the filesystem is prepared for the installation and everything can be read/
		 * written correctly.
		 */

		// check if the backend-cache-directory is writable
		self::checkRequirement('fileSystemBackendCache', (defined('PATH_WWW') && self::isRecursivelyWritable(PATH_WWW . '/backend/cache/')), $variables);

		// check if the frontend-cache-directory is writable
		self::checkRequirement('fileSystemFrontendCache', (defined('PATH_WWW') && self::isRecursivelyWritable(PATH_WWW . '/frontend/cache/')), $variables);

		// check if the frontend-files-directory is writable
		self::checkRequirement('fileSystemFrontendFiles', (defined('PATH_WWW') && self::isRecursivelyWritable(PATH_WWW . '/frontend/files/')), $variables);

		// check if the library-directory is writable
		self::checkRequirement('fileSystemLibrary', (defined('PATH_LIBRARY') && self::isWritable(PATH_LIBRARY)), $variables);

		// check if the library/external-directory is writable
		self::checkRequirement('fileSystemLibraryExternal', (defined('PATH_LIBRARY') && self::isWritable(PATH_LIBRARY . '/external')), $variables);

		// check if the installer-directory is writable
		self::checkRequirement('fileSystemInstaller', (defined('PATH_WWW') && self::isWritable(PATH_WWW . '/install/cache')), $variables);

		// does the config.base.php file exist
		self::checkRequirement('fileSystemConfig', (defined('PATH_LIBRARY') && file_exists(PATH_LIBRARY . '/config.base.php') && is_readable(PATH_LIBRARY . '/config.base.php')), $variables);

		// does the globals.base.php file exist
		self::checkRequirement('fileSystemGlobals', (defined('PATH_LIBRARY') && file_exists(PATH_LIBRARY . '/globals.base.php') && is_readable(PATH_LIBRARY . '/globals.base.php')), $variables);

		// does the globals_backend.base.php file exist
		self::checkRequirement('fileSystemGlobalsBackend', (defined('PATH_LIBRARY') && file_exists(PATH_LIBRARY . '/globals_backend.base.php') && is_readable(PATH_LIBRARY . '/globals_backend.base.php')), $variables);

		// does the globals_frontend.base.php file exist
		self::checkRequirement('fileSystemGlobalsFrontend', (defined('PATH_LIBRARY') && file_exists(PATH_LIBRARY . '/globals_frontend.base.php') && is_readable(PATH_LIBRARY . '/globals_frontend.base.php')), $variables);

		// library path exists
		self::checkRequirement('fileSystemPathLibrary', (defined('PATH_LIBRARY') && PATH_LIBRARY != ''), $variables);

		// error status
		return !in_array('nok', $variables);
	}


	/**
	 * Define path constants
	 *
	 * @return	void
	 * @param	int $step	The step wherefor the constant should be defined.
	 */
	private static function defineConstants($step)
	{
		// init library path
		$pathLibrary = null;

		// define step
		if($step != 1) $pathLibrary = (isset($_SESSION['path_library'])) ? $_SESSION['path_library'] : null;

		// guess the path to the library
		if($pathLibrary == null)
		{
			// guess the path
			self::guessLibraryPath(dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME']))), $pathLibrary);

			$count = count($pathLibrary);

			// just one found? add it into the session
			if($count == 1) $_SESSION['path_library'] = $pathLibrary[0];

			// none found means there is no Spoon
			elseif($count == 0) return false;

			// multiple
			else
			{
				// redirect
				header('Location: index.php?step=1');
				exit;
			}
		}

		// define constants
		if(!defined('PATH_WWW')) define('PATH_WWW', dirname(dirname(realpath($_SERVER['SCRIPT_FILENAME']))));
		if(!defined('PATH_LIBRARY')) define('PATH_LIBRARY', (string) $pathLibrary);

		// update session
		if(!isset($_SESSION['path_library'])) $_SESSION['path_library'] = PATH_LIBRARY;
		if(!isset($_SESSION['path_www'])) $_SESSION['path_www'] = PATH_WWW;
	}


	/**
	 * Execute this step
	 *
	 * @return	void
	 */
	public function execute()
	{
		// init vars
		$variables = array();

		// head
		$variables['head'] = file_get_contents('layout/templates/head.tpl');
		$variables['foot'] = file_get_contents('layout/templates/foot.tpl');

		// check requirements
		$validated = self::checkRequirements($variables);

		// has errors
		if(!$validated)
		{
			// assign the variable
			$variables['nextButton'] = '&nbsp;';
			$variables['requirementsStatusError'] = '';
			$variables['requirementsStatusOK'] = 'hidden';
		}

		// no errors detected
		else
		{
			header('Location: index.php?step=3');
			exit;
		}

		// set paths for template
		$variables['PATH_WWW'] = (defined('PATH_WWW')) ? PATH_WWW : '<unknown>';
		$variables['PATH_LIBRARY'] = (defined('PATH_LIBRARY')) ? PATH_LIBRARY : '<unknown>';

		// template contents
		$tpl = file_get_contents('layout/templates/2.tpl');

		// build the search & replace array
		$search = array_keys($variables);
		$replace = array_values($variables);

		// loop search values
		foreach($search as $key => $value) $search[$key] = '{$' . $value . '}';

		// build output
		$output = str_replace($search, $replace, $tpl);

		// show output
		echo $output;

		// stop the script
		exit;
	}


	/**
	 * Try to guess the location of the library based on spoon library
	 *
	 * @return	void
	 * @param	string $directory			The directory to start from.
	 * @param	array[optional] $library	An array to hold the paths that were guesed.
	 */
	private static function guessLibraryPath($directory, array &$library = null)
	{
		// init var
		$location = '';

		// loop directories
		foreach((array) glob($directory . '/*') as $filename)
		{
			// not a directory and equals 'spoon.php'
			if(!is_dir($filename) && substr($filename, -9) == 'spoon.php')
			{
				// get real path
				$path = dirname(dirname($filename));

				// only unique values should be added
				if(is_array($library))
				{
					// add
					if(!in_array($path, $library)) $library[] = $path;
				}

				// not an array
				else $library = array($path);
			}

			// directory
			elseif(is_dir($filename) && substr($filename, -4) != '.svn')
			{
				// new location
				self::guessLibraryPath($filename, $library);
			}
		}
	}


	/**
	 * This step is always allowed.
	 *
	 * @return	bool
	 */
	public static function isAllowed()
	{
		return true;
	}


	/**
	 * Check if a directory and it's sub-directories and it's subdirectories and ... are writable.
	 *
	 * @return	bool
	 * @param	string $path	The path to check.
	 */
	private static function isRecursivelyWritable($path)
	{
		// redefine argument
		$path = rtrim((string) $path, '/');

		// check if path is writable
		if(!self::isWritable($path)) return false;

		// loop child directories
		foreach((array) scandir($path) as $file)
		{
			// no '.' and '..'
			if(($file != '.') && ($file != '..'))
			{
				// directory
				if(is_dir($path . '/' . $file))
				{
					// check if children are readable
					if(!self::isRecursivelyWritable($path . '/' . $file)) return false;
				}
			}
		}

		// we were able to read all sub-directories
		return true;
	}


	/**
	 * Check if a directory is writable.
	 * The default is_writable function has problems due to Windows ACLs "bug"
	 *
	 * @return	bool
	 * @param	string $path	The path to check.
	 */
	private static function isWritable($path)
	{
		// redefine argument
		$path = rtrim((string) $path, '/');

		// create temporary file
		$file = tempnam($path, 'isWritable');

		// file has been created
		if($file !== false)
		{
			// remove temporary file
			@unlink($file);

			// file could not be created = writable
			return true;
		}

		// file could not be created = not writable
		return false;
	}
}

?>