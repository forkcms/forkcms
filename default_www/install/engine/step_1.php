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
		$errors = false;
		$variables = array();
		$variables['PATH_WWW'] = PATH_WWW;
		$variables['PATH_LIBRARY'] = PATH_LIBRARY;

		// check requirements
		self::checkRequirements($variables, $errors);

		// has errors
		if($errors)
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
 	 * Checks the requirements
 	 *
 	 * @return	bool
 	 * @param	array[optional] $variables
 	 * @param	bool[optional] $errors
	 */
	public static function checkRequirements(array &$variables = null, &$errors = false)
	{
		// define step
		$step = (isset($_GET['step']) && in_array($_GET['step'], array('1', '2', '3', '4', '5'))) ? (int) $_GET['step'] : 1;

		// define constants
		if(!defined('PATH_WWW') && !defined('PATH_LIBRARY')) self::defineConstants($step);

		// init variables
		$variables['error'] = '';

		/*
		 * At first we're going to check to see if the PHP version meets the minimum requirements
		 * for Fork CMS.
		 */

		// fetch the PHP version.
		$version = (int) str_replace('.', '', PHP_VERSION);

		// we require at least 5.2.x
		if($version >= 520)
		{
			$variables['phpVersion'] = 'ok';
			$variables['phpVersionStatus'] = 'ok';
		}

		// invalid php version
		else
		{
			$variables['phpVersion'] = 'nok';
			$variables['phpVersionStatus'] = 'not ok';
			$errors = true;
		}

		/*
		 * A couple extensions need to be loaded in order to be able to use Fork CMS. Without these
		 * extensions, we can't guarantee that everything will work.
		 */

		// check for cURL extension
		if(extension_loaded('curl'))
		{
			$variables['extensionCURL'] = 'ok';
			$variables['extensionCURLStatus'] = 'ok';
		}

		// cURL extension not found
		else
		{
			$variables['extensionCURL'] = 'nok';
			$variables['extensionCURLStatus'] = 'not ok';
			$errors = true;
		}

		// check for SimpleXML extension
		if(extension_loaded('SimpleXML'))
		{
			$variables['extensionSimpleXML'] = 'ok';
			$variables['extensionSimpleXMLStatus'] = 'ok';
		}

		// SimpleXML extension not found
		else
		{
			$variables['extensionSimpleXML'] = 'nok';
			$variables['extensionSimpleXMLStatus'] = 'not ok';
			$errors = true;
		}

		// check for SPL extension
		if(extension_loaded('SPL'))
		{
			$variables['extensionSPL'] = 'ok';
			$variables['extensionSPLStatus'] = 'ok';
		}

		// SPL extension not found
		else
		{
			$variables['extensionSPL'] = 'nok';
			$variables['extensionSPLStatus'] = 'not ok';
			$errors = true;
		}

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
				$errors = true;
			}
		}

		// PDO extension not found
		else
		{
			$variables['extensionPDO'] = 'nok';
			$variables['extensionPDOStatus'] = 'not ok';
			$errors = true;
		}

		// check for mbstring extension
		if(extension_loaded('mbstring'))
		{
			$variables['extensionMBString'] = 'ok';
			$variables['extensionMBStringStatus'] = 'ok';
		}

		// mbstring extension not found
		else
		{
			$variables['extensionMBString'] = 'nok';
			$variables['extensionMBStringStatus'] = 'not ok';
			$errors = true;
		}

		// check for iconv extension
		if(extension_loaded('iconv'))
		{
			$variables['extensionIconv'] = 'ok';
			$variables['extensionIconvStatus'] = 'ok';
		}

		// iconv extension not found
		else
		{
			$variables['extensionIconv'] = 'nok';
			$variables['extensionIconvStatus'] = 'not ok';
			$errors = true;
		}

		// check for gd extension and correct version
		if(extension_loaded('gd') && function_exists('gd_info'))
		{
			$variables['extensionGD2'] = 'ok';
			$variables['extensionGD2Status'] = 'ok';
		}

		// gd2 extension not found or version problem
		else
		{
			$variables['extensionGD2'] = 'nok';
			$variables['extensionGD2Status'] = 'not ok';
			$errors = true;
		}

		/*
		 * A couple of php.ini settings should be configured in a specific way to make sure that
		 * they don't intervene with Fork CMS.
		 */

		// check for safe mode
		if(ini_get('safe_mode') == '')
		{
			$variables['settingsSafeMode'] = 'ok';
			$variables['settingsSafeModeStatus'] = 'ok';
		}

		// safe mode is enabled (we don't want that)
		else
		{
			$variables['settingsSafeMode'] = 'nok';
			$variables['settingsSafeModeStatus'] = 'not ok';
		}

		// check for open basedir
		if(ini_get('open_basedir') == '')
		{
			$variables['settingsOpenBasedir'] = 'ok';
			$variables['settingsOpenBasedirStatus'] = 'ok';
		}

		// open basedir is enabled (we don't want that)
		else
		{
			$variables['settingsOpenBasedir'] = 'nok';
			$variables['settingsOpenBasedirStatus'] = 'not ok';
		}

		// check if the backend-cache-directory is writable
		if(is_writable(PATH_WWW .'/backend/cache/'))
		{
			$variables['fileSystemBackendCache'] = 'ok';
			$variables['fileSystemBackendCacheStatus'] = 'ok';
		}
		else
		{
			$variables['fileSystemBackendCache'] = 'nok';
			$variables['fileSystemBackendCacheStatus'] = 'not ok';
			$errors = true;
		}

		// check if the frontend-cache-directory is writable
		if(is_writable(PATH_WWW .'/frontend/cache/'))
		{
			$variables['fileSystemFrontendCache'] = 'ok';
			$variables['fileSystemFrontendCacheStatus'] = 'ok';
		}
		else
		{
			$variables['fileSystemFrontendCache'] = 'nok';
			$variables['fileSystemFrontendCacheStatus'] = 'not ok';
			$errors = true;
		}

		// check if the frontend-files-directory is writable
		if(is_writable(PATH_WWW .'/frontend/files/'))
		{
			$variables['fileSystemFrontendFiles'] = 'ok';
			$variables['fileSystemFrontendFilesStatus'] = 'ok';
		}
		else
		{
			$variables['fileSystemFrontendFiles'] = 'nok';
			$variables['fileSystemFrontendFilesStatus'] = 'not ok';
			$errors = true;
		}

		// check if the library-directory is writable
		if(is_writable(PATH_LIBRARY))
		{
			$variables['fileSystemLibrary'] = 'ok';
			$variables['fileSystemLibraryStatus'] = 'ok';
		}
		else
		{
			$variables['fileSystemLibrary'] = 'nok';
			$variables['fileSystemLibraryStatus'] = 'not ok';
			$errors = true;
		}

		// check if the installer-directory is writable
		if(is_writable(PATH_WWW .'/install'))
		{
			$variables['fileSystemInstaller'] = 'ok';
			$variables['fileSystemInstallerStatus'] = 'ok';
		}
		else
		{
			$variables['fileSystemInstaller'] = 'nok';
			$variables['fileSystemInstallerStatus'] = 'not ok';
			$errors = true;
		}

		// does the globals.example.php file exist
		if(file_exists(PATH_LIBRARY .'/globals.example.php') && is_readable(PATH_LIBRARY .'/globals.example.php'))
		{
			$variables['fileSystemGlobals'] = 'ok';
			$variables['fileSystemGlobalsStatus'] = 'ok';
		}
		else
		{
			$variables['fileSystemGlobals'] = 'nok';
			$variables['fileSystemGlobalsStatus'] = 'not ok';
			$errors = true;
		}

		// does the globals_backend.example.php file exist
		if(file_exists(PATH_LIBRARY .'/globals_backend.example.php') && is_readable(PATH_LIBRARY .'/globals_backend.example.php'))
		{
			$variables['fileSystemGlobalsBackend'] = 'ok';
			$variables['fileSystemGlobalsBackendStatus'] = 'ok';
		}
		else
		{
			$variables['fileSystemGlobalsBackend'] = 'nok';
			$variables['fileSystemGlobalsBackendStatus'] = 'not ok';
			$errors = true;
		}

		// does the globals_frontend.example.php file exist
		if(file_exists(PATH_LIBRARY .'/globals_frontend.example.php') && is_readable(PATH_LIBRARY .'/globals_frontend.example.php'))
		{
			$variables['fileSystemGlobalsFrontend'] = 'ok';
			$variables['fileSystemGlobalsFrontendStatus'] = 'ok';
		}
		else
		{
			$variables['fileSystemGlobalsFrontend'] = 'nok';
			$variables['fileSystemGlobalsFrontendStatus'] = 'not ok';
			$errors = true;
		}

		// library path exists
		if(PATH_LIBRARY != '')
		{
			$variables['filesystemPathLibrary'] = 'ok';
			$variables['filesystemPathLibraryStatus'] = 'ok';
		}
		else
		{
			$variables['filesystemPathLibrary'] = 'nok';
			$variables['filesystemPathLibraryStatus'] = 'not ok';
			$errors = true;
		}

		// error status
		return !$errors;
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
			self::guessLibraryPath(realpath($_SERVER['DOCUMENT_ROOT'] .'/..'), $pathLibrary);

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
			elseif(is_dir($filename))
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