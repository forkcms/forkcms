<?php

class InstallerStep1 extends InstallerStep
{
	/**
	 * Execute this step
	 *
	 * @return	void
	 */
	public function execute()
	{
		// init vars
		$variables = array();

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
			// button
			$variables['nextButton'] = '<input id="installerButton" class="inputButton button mainButton" type="submit" name="installer" value="Next" />';
			$variables['requirementsStatusError'] = 'hidden';
			$variables['requirementsStatusOK'] = '';
		}

		// set paths for template
		$variables['PATH_WWW'] = PATH_WWW;
		$variables['PATH_LIBRARY'] = PATH_LIBRARY;

		// template contents
		$tpl = file_get_contents('layout/templates/1.tpl');

		// build the search & replace array
		$search = array_keys($variables);
		$replace = array_values($variables);

		// loop search values
		foreach($search as $key => $value) $search[$key] = '{$'. $value .'}';

		// build output
		$output = str_replace($search, $replace, $tpl);

		// show
		echo $output;

		// stop the script
		exit;
	}


	/**
	 * Check if a specific requirement is satisfied
	 *
	 * @return	boolean
	 * @param	string $variable
	 * @param	bool $requirement
 	 * @param	array[optional] $variables
	 */
	public static function checkRequirement($variable, $requirement, array &$variables = null)
	{
		// requirement satisfied
		if($requirement)
		{
			$variables[$variable] = 'ok';
			$variables[$variable .'Status'] = 'ok';
			return true;
		}

		// requirement not satisfied
		else
		{
			$variables[$variable] = 'nok';
			$variables[$variable .'Status'] = 'not ok';
			return false;
		}
	}


	/**
 	 * Checks the requirements
 	 *
 	 * @return	bool
 	 * @param	array[optional] $variables
	 */
	public static function checkRequirements(array &$variables = null)
	{
		// define step
		$step = (isset($_GET['step']) && in_array($_GET['step'], array('1', '2', '3', '4', '5'))) ? (int) $_GET['step'] : 1;

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
		self::checkRequirement('settingsSafeMode', ini_get('safe_mode') == '', $variables);

		// check for open basedir
		self::checkRequirement('settingsOpenBasedir', ini_get('open_basedir') == '', $variables);

		/*
		 * Make sure the filesystem is prepared for the installation and everything can be read/
		 * written correctly.
		 */

		// check if the backend-cache-directory is writable
		self::checkRequirement('fileSystemBackendCache', is_writable(PATH_WWW .'/backend/cache/'), $variables);

		// check if the frontend-cache-directory is writable
		self::checkRequirement('fileSystemFrontendCache', is_writable(PATH_WWW .'/frontend/cache/'), $variables);

		// check if the frontend-files-directory is writable
		self::checkRequirement('fileSystemFrontendFiles', is_writable(PATH_WWW .'/frontend/files/'), $variables);

		// check if the library-directory is writable
		self::checkRequirement('fileSystemLibrary', is_writable(PATH_LIBRARY), $variables);

		// check if the installer-directory is writable
		self::checkRequirement('fileSystemInstaller', is_writable(PATH_WWW .'/install'), $variables);

		// does the config.example.php file exist
		self::checkRequirement('fileSystemConfig', file_exists(PATH_LIBRARY .'/config.example.php') && is_readable(PATH_LIBRARY .'/config.example.php'), $variables);

		// does the globals.example.php file exist
		self::checkRequirement('fileSystemGlobals', file_exists(PATH_LIBRARY .'/globals.example.php') && is_readable(PATH_LIBRARY .'/globals.example.php'), $variables);

		// does the globals_backend.example.php file exist
		self::checkRequirement('fileSystemGlobalsBackend', file_exists(PATH_LIBRARY .'/globals_backend.example.php') && is_readable(PATH_LIBRARY .'/globals_backend.example.php'), $variables);

		// does the globals_frontend.example.php file exist
		self::checkRequirement('fileSystemGlobalsFrontend', file_exists(PATH_LIBRARY .'/globals_frontend.example.php') && is_readable(PATH_LIBRARY .'/globals_frontend.example.php'), $variables);

		// library path exists
		self::checkRequirement('fileSystemPathLibrary', PATH_LIBRARY != '', $variables);

		// error status
		return !in_array('nok', $variables);
	}


	/**
	 * Define path constants
	 *
	 * @return	void
	 */
	private static function defineConstants($step)
	{
		// init library path
		$pathLibrary = '';

		// define step
		if($step != 1) $pathLibrary = (isset($_SESSION['path_library'])) ? $_SESSION['path_library'] : '';

		// guess the path to the library
		if($pathLibrary == '')
		{
			// guess the path
			self::guessLibraryPath(realpath(str_replace('/install/index.php', '', $_SERVER['SCRIPT_FILENAME'])) .'/..', $pathLibrary);

			// add it to the session
			$_SESSION['path_library'] = $pathLibrary;
		}

		// define constants
		if(!defined('PATH_WWW')) define('PATH_WWW', realpath(str_replace('/index.php', '/..', realpath($_SERVER['SCRIPT_FILENAME']))));
		if(!defined('PATH_LIBRARY')) define('PATH_LIBRARY', $pathLibrary);

		// update session
		if(!isset($_SESSION['path_library'])) $_SESSION['path_library'] = PATH_LIBRARY;
		if(!isset($_SESSION['path_www'])) $_SESSION['path_www'] = PATH_WWW;
	}


	/**
	 * Try to guess the location of the library based on spoon library
	 *
	 * @return	void
	 * @param	string $directory
	 * @param	string[optional] $library
	 */
	private static function guessLibraryPath($directory, &$library = null)
	{
		// init var
		$location = '';

		// loop directories
		foreach(glob($directory .'/*') as $filename)
		{
			// not a directory and equals 'spoon.php'
			if(!is_dir($filename) && substr($filename, -9) == 'spoon.php')
			{
				$library = realpath(str_replace('spoon.php', '..', $filename));
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
}

?>