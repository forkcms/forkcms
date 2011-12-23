<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will initiate the frontend-application
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class FrontendInit
{
	/**
	 * Current type
	 *
	 * @var	string
	 */
	private $type;

	/**
	 * @param string $type The type of init to load, possible values are: frontend, frontend_ajax, frontend_js.
	 */
	public function __construct($type)
	{
		$allowedTypes = array('frontend', 'frontend_ajax', 'frontend_js');
		$type = (string) $type;

		// check if this is a valid type
		if(!in_array($type, $allowedTypes)) exit('Invalid init-type');
		$this->type = $type;

		// register the autoloader
		spl_autoload_register(array('FrontendInit', 'autoLoader'));

		// set some ini-options
		ini_set('pcre.backtrack_limit', 999999999);
		ini_set('pcre.recursion_limit', 999999999);
		ini_set('memory_limit', '64M');

		// set a default timezone if no one was set by PHP.ini
		if(ini_get('date.timezone') == '') date_default_timezone_set('Europe/Brussels');

		/**
		 * At first we enable the error reporting. Later on it will be disabled based on the
		 * value of SPOON_DEBUG, but for now it's required to see possible errors while trying
		 * to include the globals file(s).
		 */
		error_reporting(E_ALL | E_STRICT);
		ini_set('display_errors', 'On');

		$this->requireGlobals();

		// get last modified time for globals
		$lastModifiedTime = @filemtime(PATH_LIBRARY . '/globals.php');

		// reset lastmodified time if needed (SPOON_DEBUG is enabled or we don't get a decent timestamp)
		if($lastModifiedTime === false || SPOON_DEBUG) $lastModifiedTime = time();

		// define as a constant
		define('LAST_MODIFIED_TIME', $lastModifiedTime);

		$this->definePaths();
		$this->defineURLs();
		$this->setIncludePath();
		$this->setDebugging();

		// require spoon
		require_once 'spoon/spoon.php';

		$this->requireFrontendClasses();
		SpoonFilter::disableMagicQuotes();
	}

	/**
	 * Autoloader for the frontend
	 *
	 * @param string $className The name of the class to require.
	 */
	public static function autoLoader($className)
	{
		// redefine
		$className = strtolower((string) $className);

		// init var
		$pathToLoad = '';

		// exceptions
		$exceptions = array();
		$exceptions['frontend'] = FRONTEND_CORE_PATH . '/engine/frontend.php';
		$exceptions['frontendbaseajaxaction'] = FRONTEND_CORE_PATH . '/engine/base.php';
		$exceptions['frontendbaseconfig'] = FRONTEND_CORE_PATH . '/engine/base.php';
		$exceptions['frontendbaseobject'] = FRONTEND_CORE_PATH . '/engine/base.php';
		$exceptions['frontendblockextra'] = FRONTEND_CORE_PATH . '/engine/block.php';
		$exceptions['frontendblockwidget'] = FRONTEND_CORE_PATH . '/engine/block.php';
		$exceptions['frontendtemplatecompiler'] = FRONTEND_CORE_PATH . '/engine/template_compiler.php';

		// is it an exception
		if(isset($exceptions[$className])) $pathToLoad = $exceptions[$className];

		// frontend
		elseif(substr($className, 0, 8) == 'frontend') $pathToLoad = FRONTEND_CORE_PATH . '/engine/' . str_replace('frontend', '', $className) . '.php';

		// file check in core
		if($pathToLoad != '' && SpoonFile::exists($pathToLoad)) require_once $pathToLoad;

		// check if module file exists
		else
		{
			// we'll need the original class name again, with the uppercases
			$className = func_get_arg(0);

			// split in parts, if nothing is found we stop processing
			if(!preg_match_all('/[A-Z][a-z0-9]*/', $className, $parts)) return;

			// the real matches
			$parts = $parts[0];

			// doublecheck that we are looking for a frontend class, of that isn't the case we should stop.
			$root = array_shift($parts);
			if(strtolower($root) != 'frontend') return;

			foreach($parts as $i => $part)
			{
				// skip the first
				if($i == 0) continue;

				// action
				$action = strtolower(implode('_', $parts));

				// module
				$module = '';
				for($j = 0; $j < $i; $j++) $module .= strtolower($parts[$j]) . '_';

				// fix action & module
				$action = substr($action, strlen($module));
				$module = substr($module, 0, -1);

				// check the actions, engine & widgets directories
				foreach(array('actions', 'engine', 'widgets') as $dir)
				{
					// file to be loaded
					$pathToLoad = FRONTEND_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $action . '.php';

					// if it exists, load it!
					if($pathToLoad != '' && SpoonFile::exists($pathToLoad))
					{
						require_once $pathToLoad;
						break;
					}
				}
			}
		}
	}

	/**
	 * Define paths
	 */
	private function definePaths()
	{
		// fix the Application setting
		if($this->type == 'frontend_js') define('APPLICATION', 'frontend');
		elseif($this->type == 'frontend_ajax') define('APPLICATION', 'frontend');

		// general paths
		define('FRONTEND_PATH', PATH_WWW . '/' . APPLICATION);
		define('FRONTEND_CACHE_PATH', FRONTEND_PATH . '/cache');
		define('FRONTEND_CORE_PATH', FRONTEND_PATH . '/core');
		define('FRONTEND_MODULES_PATH', FRONTEND_PATH . '/modules');
		define('FRONTEND_FILES_PATH', FRONTEND_PATH . '/files');
	}

	/**
	 * Define URLs
	 */
	private function defineURLs()
	{
		define('FRONTEND_CORE_URL', '/' . APPLICATION . '/core');
		define('FRONTEND_CACHE_URL', '/' . APPLICATION . '/cache');
		define('FRONTEND_FILES_URL', '/' . APPLICATION . '/files');
	}

	/**
	 * A custom error-handler so we can handle warnings about undefined labels
	 *
	 * @param int $errorNumber The level of the error raised, as an integer.
	 * @param string $errorString The error message, as a string.
	 * @return bool
	 */
	public static function errorHandler($errorNumber, $errorString)
	{
		// redefine
		$errorNumber = (int) $errorNumber;
		$errorString = (string) $errorString;

		// is this an undefined index?
		if(mb_substr_count($errorString, 'Undefined index:') > 0)
		{
			// cleanup
			$index = trim(str_replace('Undefined index:', '', $errorString));

			// get the type
			$type = mb_substr($index, 0, 3);

			// is the index locale?
			if(in_array($type, array('act', 'err', 'lbl', 'msg'))) echo '{$' . $index . '}';

			// return false, so the standard error handler isn't bypassed
			else return false;
		}

		// return false, so the standard error handler isn't bypassed
		else return false;
	}

	/**
	 * This method will be called by the Spoon Exceptionhandler and is specific for exceptions thrown in AJAX-actions
	 *
	 * @param object $exception The exception that was thrown.
	 * @param string $output The output that should be mailed.
	 */
	public static function exceptionAJAXHandler($exception, $output)
	{
		// redefine
		$output = (string) $output;

		// set headers
		SpoonHTTP::setHeaders('content-type: application/json');

		// create response array
		$response = array('code' => ($exception->getCode() != 0) ? $exception->getCode() : 500, 'message' => $exception->getMessage());

		// output to the browser
		echo json_encode($response);

		// stop script execution
		exit;
	}

	/**
	 * This method will be called by the Spoon Exceptionhandler
	 *
	 * @param object $exception The exception that was thrown.
	 * @param string $output The output that should be mailed.
	 */
	public static function exceptionHandler($exception, $output)
	{
		$output = (string) $output;

		// mail it?
		if(SPOON_DEBUG_EMAIL != '')
		{
			// e-mail headers
			$headers = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/html; charset=iso-8859-15\n";
			$headers .= "X-Priority: 3\n";
			$headers .= "X-MSMail-Priority: Normal\n";
			$headers .= "X-Mailer: SpoonLibrary Webmail\n";
			$headers .= "From: Spoon Library <no-reply@spoon-library.com>\n";

			// send email
			@mail(SPOON_DEBUG_EMAIL, 'Exception Occured (' . SITE_DOMAIN . ')', $output, $headers);
		}

		// build HTML for nice error
		$html = '<html><body>Something went wrong.</body></html>';

		// output
		echo $html;
		exit;
	}

	/**
	 * This method will be called by the Spoon Exceptionhandler and is specific for exceptions thrown in JS-files parsed through PHP
	 *
	 * @param object $exception The exception that was thrown.
	 * @param string $output The output that should be mailed.
	 */
	public static function exceptionJSHandler($exception, $output)
	{
		// redefine
		$output = (string) $output;

		// set correct headers
		SpoonHTTP::setHeaders('content-type: application/javascript');

		// output
		echo '// ' . $exception->getMessage();
		exit;
	}

	/**
	 * Require all needed classes
	 */
	private function requireFrontendClasses()
	{
		switch($this->type)
		{
			case 'frontend':
			case 'frontend_ajax':
				require_once FRONTEND_CORE_PATH . '/engine/template_custom.php';
				require_once FRONTEND_PATH . '/modules/tags/engine/model.php';
				break;
		}
	}

	/**
	 * Require globals-file
	 */
	private function requireGlobals()
	{
		// fetch config
		$installed[] = @include_once dirname(__FILE__) . '/cache/config/config.php';

		// load the globals
		$installed[] = @include_once INIT_PATH_LIBRARY . '/globals.php';
		$installed[] = @include_once INIT_PATH_LIBRARY . '/globals_backend.php';
		$installed[] = @include_once INIT_PATH_LIBRARY . '/globals_frontend.php';

		// something could not be loaded
		if(in_array(false, $installed))
		{
			// installation folder
			$installer = dirname(__FILE__) . '/../install/cache';

			// Fork has not yet been installed
			if(file_exists($installer) && is_dir($installer) && !file_exists($installer . '/installed.txt'))
			{
				// redirect to installer
				header('Location: /install');
			}

			// we can nog load configuration file, however we can not run installer
			echo 'Required configuration files are missing. Try deleting current files, clearing your database, re-uploading <a href="http://www.fork-cms.be">Fork CMS</a> and <a href="/install">rerun the installer</a>.';
			exit;
		}
	}

	/**
	 * Set debugging
	 */
	private function setDebugging()
	{
		// debugging enabled
		if(SPOON_DEBUG)
		{
			// set error reporting as high as possible
			error_reporting(E_ALL | E_STRICT);

			// show errors on the screen
			ini_set('display_errors', 'On');

			// in debug mode notices are triggered when using non existing locale, so we use a custom errorhandler to cleanup the message
			set_error_handler(array('FrontendInit', 'errorHandler'));
		}

		// debugging disabled
		else
		{
			// set error reporting as low as possible
			error_reporting(0);

			// don't show error on the screen
			ini_set('display_errors', 'Off');

			// add callback for the spoon exceptionhandler
			switch($this->type)
			{
				case 'backend_ajax':
					define('SPOON_EXCEPTION_CALLBACK', __CLASS__ . '::exceptionAJAXHandler');
					break;

				case 'backend_js':
					define('SPOON_EXCEPTION_CALLBACK', __CLASS__ . '::exceptionJSHandler');
					break;

				default:
					define('SPOON_EXCEPTION_CALLBACK', __CLASS__ . '::exceptionHandler');
			}
		}
	}

	/**
	 * Set include path
	 */
	private function setIncludePath()
	{
		// prepend the libary and document_root to the existing include path
		set_include_path(PATH_LIBRARY . PATH_SEPARATOR . PATH_WWW . PATH_SEPARATOR . get_include_path());
	}
}
