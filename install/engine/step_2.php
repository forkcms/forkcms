<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Step 2 of the Fork installer
 *
 * @author Davy Hellemans <davy@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Dieter Vanden Eynde <dieter@dieterve.be>
 */
class InstallerStep2 extends InstallerStep
{
	/**
	 * Requirements error statuses
	 */
	const STATUS_OK = 'ok';
	const STATUS_WARNING = 'warning';
	const STATUS_ERROR = 'error';

	/**
	 * Array containing all variables to be parsed in the template.
	 *
	 * @var	array
	 */
	private static $variables = array();

	/**
	 * Check if a specific requirement is satisfied
	 *
	 * @param string $variable The "name" of the check.
	 * @param bool $requirement The result of the check.
	 * @param string $severity The severity of the requirement.
	 * @return bool
	 */
	public static function checkRequirement($variable, $requirement, $severity = self::STATUS_ERROR)
	{
		// set status
		self::$variables[$variable] = $requirement ? self::STATUS_OK : $severity;
		return self::$variables[$variable] == self::STATUS_OK;
	}

	/**
	 * Checks the requirements
	 *
	 * @return bool
	 */
	public static function checkRequirements()
	{
		// define step
		$step = (isset($_GET['step']) && in_array($_GET['step'], array('1', '2', '3', '4', '5', '6', '7'))) ? (int) $_GET['step'] : 1;

		// define constants
		if(!defined('PATH_WWW') && !defined('PATH_LIBRARY')) self::defineConstants($step);

		/*
		 * At first we're going to check to see if the PHP version meets the minimum requirements
		 * for Fork CMS. We require at least PHP 5.3.3, but not 5.3.16, because Symfony won't work properly
		 */
		self::checkRequirement('phpVersion', version_compare(PHP_VERSION, '5.3.3-whatever', '>='), self::STATUS_ERROR);
		self::checkRequirement('phpVersion', version_compare(PHP_VERSION, '5.3.16', '!='), self::STATUS_ERROR);

		// Fork can't be installed in subfolders, so we should check that.
		self::checkRequirement('subfolder', (substr($_SERVER['REQUEST_URI'], 0, 18) == '/install/index.php'), self::STATUS_ERROR);

		/*
		 * A couple extensions need to be loaded in order to be able to use Fork CMS. Without these
		 * extensions, we can't guarantee that everything will work.
		 */
		self::checkRequirement('extensionCURL', extension_loaded('curl'), self::STATUS_ERROR);
		self::checkRequirement('extensionLibXML', extension_loaded('libxml'), self::STATUS_ERROR);
		self::checkRequirement('extensionDOM', extension_loaded('dom'), self::STATUS_ERROR);
		self::checkRequirement('extensionSimpleXML', extension_loaded('SimpleXML'), self::STATUS_ERROR);
		self::checkRequirement('extensionSPL', extension_loaded('SPL'), self::STATUS_ERROR);
		self::checkRequirement('extensionPDO', extension_loaded('PDO'), self::STATUS_ERROR);
		self::checkRequirement('extensionPDOMySQL', extension_loaded('PDO') && in_array('mysql', PDO::getAvailableDrivers()), self::STATUS_ERROR);
		self::checkRequirement('extensionMBString', extension_loaded('mbstring'), self::STATUS_ERROR);
		self::checkRequirement('extensionIconv', extension_loaded('iconv'), self::STATUS_ERROR);
		self::checkRequirement('extensionGD2', extension_loaded('gd') && function_exists('gd_info'), self::STATUS_ERROR);
		self::checkRequirement('extensionJSON', extension_loaded('json'), self::STATUS_ERROR);
		$pcreVersion = defined('PCRE_VERSION') ? (float) PCRE_VERSION : null;
		self::checkRequirement('extensionPCRE', (extension_loaded('pcre') && (null !== $pcreVersion && $pcreVersion > 8.0)), self::STATUS_ERROR);

		/*
		 * A couple of php.ini settings should be configured in a specific way to make sure that
		 * they don't intervene with Fork CMS.
		 */
		self::checkRequirement('settingsSafeMode', ini_get('safe_mode') == '', self::STATUS_WARNING);
		self::checkRequirement('settingsOpenBasedir', ini_get('open_basedir') == '', self::STATUS_WARNING);
		self::checkRequirement('settingsDateTimezone', (ini_get('date.timezone') == '' || (in_array(date_default_timezone_get(), DateTimeZone::listIdentifiers()))), self::STATUS_WARNING);

		/*
		 * Some functions should be available
		 */
		self::checkRequirement('functionJsonEncode', function_exists('json_encode'), self::STATUS_ERROR);
		self::checkRequirement('functionSessionStart', function_exists('session_start'), self::STATUS_ERROR);
		self::checkRequirement('functionCtypeAlpha', function_exists('ctype_alpha'), self::STATUS_ERROR);
		self::checkRequirement('functionTokenGetAll', function_exists('token_get_all'), self::STATUS_ERROR);
		self::checkRequirement('functionSimplexmlImportDom', function_exists('simplexml_import_dom'), self::STATUS_ERROR);

		/*
		 * Make sure the filesystem is prepared for the installation and everything can be read/
		 * written correctly.
		 */
		self::checkRequirement('fileSystemBackendCache', defined('PATH_WWW') && self::isRecursivelyWritable(PATH_WWW . '/backend/cache/'), self::STATUS_ERROR);
		self::checkRequirement('fileSystemBackendModules', defined('PATH_WWW') && self::isWritable(PATH_WWW . '/backend/modules/'), self::STATUS_WARNING);
		self::checkRequirement('fileSystemFrontendCache', defined('PATH_WWW') && self::isRecursivelyWritable(PATH_WWW . '/frontend/cache/'), self::STATUS_ERROR);
		self::checkRequirement('fileSystemFrontendFiles', defined('PATH_WWW') && self::isRecursivelyWritable(PATH_WWW . '/frontend/files/'), self::STATUS_ERROR);
		self::checkRequirement('fileSystemFrontendModules', defined('PATH_WWW') && self::isWritable(PATH_WWW . '/frontend/modules/'), self::STATUS_WARNING);
		self::checkRequirement('fileSystemFrontendThemes', defined('PATH_WWW') && self::isWritable(PATH_WWW . '/frontend/themes/'), self::STATUS_WARNING);
		self::checkRequirement('fileSystemLibrary', defined('PATH_LIBRARY') && self::isWritable(PATH_LIBRARY), self::STATUS_ERROR);
		self::checkRequirement('fileSystemLibraryExternal', defined('PATH_LIBRARY') && self::isWritable(PATH_LIBRARY . '/external'), self::STATUS_WARNING);
		self::checkRequirement('fileSystemInstaller', defined('PATH_WWW') && self::isWritable(PATH_WWW . '/install/cache'), self::STATUS_ERROR);
		self::checkRequirement('fileSystemAppConfig', defined('PATH_WWW') && self::isWritable(PATH_WWW . '/app/config/'), self::STATUS_ERROR);
		self::checkRequirement('fileSystemParameters', defined('PATH_LIBRARY') && file_exists(PATH_LIBRARY . '/parameters.base.yml') && is_readable(PATH_LIBRARY . '/parameters.base.yml'), self::STATUS_ERROR);
		self::checkRequirement('fileSystemPathLibrary', defined('PATH_LIBRARY') && PATH_LIBRARY != '', self::STATUS_ERROR);

		/*
		 * Ensure that Apache .htaccess file is written and mod_rewrite does its job
		 */
		self::checkRequirement('modRewrite', (bool) (getenv('MOD_REWRITE') || getenv('REDIRECT_MOD_REWRITE')), self::STATUS_WARNING);

		// error status
		return !in_array(self::STATUS_ERROR, self::$variables);
	}

	/**
	 * Define path constants
	 *
	 * @param int $step The step wherefore the constant should be defined.
	 */
	private static function defineConstants($step)
	{
		// define constants
		if(!defined('PATH_WWW'))
		{
			define('PATH_WWW', dirname(realpath($_SERVER['SCRIPT_FILENAME'])));
		}

		if(!defined('PATH_LIBRARY'))
		{
			define('PATH_LIBRARY', (string) $_SESSION['path_library']);
		}

		// update session
		if(!isset($_SESSION['path_library'])) $_SESSION['path_library'] = PATH_LIBRARY;
		if(!isset($_SESSION['path_www'])) $_SESSION['path_www'] = PATH_WWW;
	}

	/**
	 * Execute this step
	 */
	public function execute()
	{
		// init vars
		self::$variables = array();

		// head
		self::$variables['head'] = file_get_contents(__DIR__ . '/../layout/templates/head.tpl');
		self::$variables['foot'] = file_get_contents(__DIR__ . '/../layout/templates/foot.tpl');

		// next step
		self::$variables['step3'] = 'index.php?step=3';

		// check requirements
		self::checkRequirements();

		// get template contents
		$tpl = file_get_contents(__DIR__ . '/../layout/templates/step_2.tpl');

		// has errors
		if(in_array(self::STATUS_ERROR, self::$variables)) self::$variables[self::STATUS_ERROR] = true;

		// has warnings
		elseif(in_array(self::STATUS_WARNING, self::$variables)) self::$variables[self::STATUS_WARNING] = true;

		// no errors detected
		else
		{
			header('Location: ' . self::$variables['step3']);
			exit;
		}

		// set paths for template
		self::$variables['PATH_WWW'] = (defined('PATH_WWW')) ? PATH_WWW : '<unknown>';
		self::$variables['PATH_LIBRARY'] = (defined('PATH_LIBRARY')) ? PATH_LIBRARY : '<unknown>';

		// build the search & replace array
		$search = array_keys(self::$variables);
		$replace = array_values(self::$variables);

		// loop search values
		foreach($search as $key => $value)
		{
			// parse variables
			$tpl = str_replace('{$' . $value . '}', $replace[$key], $tpl);

			// assign options
			$tpl = preg_replace('/\{option:\!(' . $value . ')\}(.*?)\{\/option:\!\\1\}/is', (!$replace[$key] ? '\\2' : ''), $tpl);
			$tpl = preg_replace('/\{option:(' . $value . ')\}(.*?)\{\/option:\\1\}/is', ($replace[$key] ? '\\2' : ''), $tpl);
		}

		// assign leftover options
		$tpl = preg_replace('/\{option:!(.*?)\}(.*?)\{\/option:!\\1\}/is', '\\2', $tpl);
		$tpl = preg_replace('/\{option:(.*?)\}(.*?)\{\/option:\\1\}/is', '', $tpl);

		// ignore comments
		$tpl = preg_replace('/\{\*(?!.*?\{\*).*?\*\}/s', '', $tpl);

		// show output
		echo $tpl;

		// stop the script
		exit;
	}

	/**
	 * This step is only allowed if the library path is known.
	 *
	 * @return bool
	 */
	public static function isAllowed()
	{
		return InstallerStep1::isAllowed() && isset($_SESSION['path_library']);
	}

	/**
	 * Check if a directory and it's sub-directories and it's subdirectories and ... are writable.
	 *
	 * @param string $path The path to check.
	 * @return bool
	 */
	private static function isRecursivelyWritable($path)
	{
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
	 * @param string $path The path to check.
	 * @return bool
	 */
	private static function isWritable($path)
	{
		// redefine argument
		$path = rtrim((string) $path, '/');

		// create random file
		$file = uniqid() . '.tmp';

		$return = @file_put_contents($path . '/' . $file, 'temporary file', FILE_APPEND);

		if($return === false) return false;

		// unlink the random file
		@unlink($path . '/' . $file);

		// return
		return true;
	}
}
