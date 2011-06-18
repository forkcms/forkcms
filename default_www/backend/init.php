<?php

/**
 * This class will initiate the backend-application
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendInit
{
	/**
	 * Current type
	 *
	 * @var	string
	 */
	private $type;


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	string $type	The type of init to load, possible values are: backend, backend_ajax, backend_cronjob, backend_js.
	 */
	public function __construct($type)
	{
		// init vars
		$allowedTypes = array('backend', 'backend_direct', 'backend_ajax', 'backend_js', 'backend_cronjob');
		$type = (string) $type;

		// check if this is a valid type
		if(!in_array($type, $allowedTypes)) exit('Invalid init-type');

		// set type
		$this->type = $type;

		// register the autoloader
		spl_autoload_register(array('BackendInit', 'autoLoader'));

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

		// require globals
		$this->requireGlobals();

		// define constants
		$this->definePaths();
		$this->defineURLs();

		// set include path
		$this->setIncludePath();

		// set debugging
		$this->setDebugging();

		// require spoon
		require_once 'spoon/spoon.php';

		// require backend-classes
		$this->requireBackendClasses();

		// disable magic quotes
		SpoonFilter::disableMagicQuotes();

		// start session
		$this->initSession();
	}


	/**
	 * Autoloader for the backend
	 *
	 * @return	void
	 * @param	string $className	The name of the class to require.
	 */
	public static function autoLoader($className)
	{
		// redefine
		$className = strtolower((string) $className);

		// init var
		$pathToLoad = '';

		// exceptions
		$exceptions['backend'] = BACKEND_CORE_PATH . '/engine/backend.php';
		$exceptions['backendajaxaction'] = BACKEND_CORE_PATH . '/engine/ajax_action.php';
		$exceptions['backendbaseajaxaction'] = BACKEND_CORE_PATH . '/engine/base.php';
		$exceptions['backenddatagriddb'] = BACKEND_CORE_PATH . '/engine/datagrid.php';
		$exceptions['backenddatagridarray'] = BACKEND_CORE_PATH . '/engine/datagrid.php';
		$exceptions['backendbaseconfig'] = BACKEND_CORE_PATH . '/engine/base.php';
		$exceptions['backendbasecronjob'] = BACKEND_CORE_PATH . '/engine/base.php';
		$exceptions['backendpagesmodel'] = BACKEND_MODULES_PATH . '/pages/engine/model.php';
		$exceptions['fl'] = FRONTEND_CORE_PATH . '/engine/language.php';

		// is it an exception
		if(isset($exceptions[$className])) $pathToLoad = $exceptions[$className];

		// backend
		elseif(substr($className, 0, 7) == 'backend') $pathToLoad = BACKEND_CORE_PATH . '/engine/' . str_replace('backend', '', $className) . '.php';

		// frontend
		elseif(substr($className, 0, 8) == 'frontend') $pathToLoad = FRONTEND_CORE_PATH . '/engine/' . str_replace('frontend', '', $className) . '.php';

		// file check in core
		if($pathToLoad != '' && SpoonFile::exists($pathToLoad)) require_once $pathToLoad;

		// check if module file exists
		else
		{
			// we'll need the original class name again, with the uppercases
			$className = func_get_arg(0);

			// split in parts
			if(preg_match_all('/[A-Z][a-z0-9]*/', $className, $parts))
			{
				// the real matches
				$parts = $parts[0];

				// get root path constant and see if it exists
				$rootPath = strtoupper(array_shift($parts)) . '_PATH';
				if(defined($rootPath))
				{
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
						$action = str_replace($module, '', $action);
						$module = substr($module, 0, -1);

						// check the actions, engine & widgets directories
						foreach(array('actions', 'engine', 'widgets') as $dir)
						{
							// file to be loaded
							$pathToLoad = constant($rootPath) . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $action . '.php';

							// if it exists, load it!
							if($pathToLoad != '' && SpoonFile::exists($pathToLoad))
							{
								require_once $pathToLoad;
								break 2;
							}
						}
					}
				}
			}
		}
	}


	/**
	 * Define paths
	 *
	 * @return	void
	 */
	private function definePaths()
	{
		// fix the Application setting
		if($this->type == 'backend_ajax') define('APPLICATION', 'backend');
		if($this->type == 'backend_js') define('APPLICATION', 'backend');
		if($this->type == 'backend_cronjob') define('APPLICATION', 'backend');

		// general paths
		define('BACKEND_PATH', PATH_WWW . '/' . APPLICATION);
		define('BACKEND_CACHE_PATH', BACKEND_PATH . '/cache');
		define('BACKEND_CORE_PATH', BACKEND_PATH . '/core');
		define('BACKEND_MODULES_PATH', BACKEND_PATH . '/modules');

		define('FRONTEND_PATH', PATH_WWW . '/frontend');
		define('FRONTEND_CACHE_PATH', FRONTEND_PATH . '/cache');
		define('FRONTEND_CORE_PATH', FRONTEND_PATH . '/core');
		define('FRONTEND_MODULES_PATH', FRONTEND_PATH . '/modules');
		define('FRONTEND_FILES_PATH', FRONTEND_PATH . '/files');
	}


	/**
	 * Define URLs
	 *
	 * @return	void
	 */
	private function defineURLs()
	{
		define('BACKEND_CORE_URL', '/backend/core');
		define('BACKEND_CACHE_URL', '/backend/cache');

		define('FRONTEND_FILES_URL', '/frontend/files');
	}


	/**
	 * A custom error-handler so we can handle warnings about undefined labels
	 *
	 * @return	bool
	 * @param	int $errorNumber		The level of the error raised, as an integer.
	 * @param	string $errorString		The error message, as a string.
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
	 * @return	void
	 * @param	object $exception	The exception that was thrown.
	 * @param	string $output		The output that should be mailed.
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
	 * @return	void
	 * @param	object $exception	The exception that was thrown.
	 * @param	string $output		The output that should be mailed.
	 */
	public static function exceptionHandler($exception, $output)
	{
		// redefine
		$exception = $exception;
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
		$html = '
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
					<title>Fork CMS - Error</title>
					<style type="text/css" media="screen">

						body {
							background: #FFF;
							font-family: Arial, sans-serif;
							font-size: 13px;
							text-align: center;
							width: 75%;
							margin: 0 auto;
						}

						p {
							padding: 0 0 12px;
							margin: 0;
						}

						h2 {
							font-size: 20px;
							margin: 0
							padding: 0 0 10px;
						}
					</style>
				</head>
				<body>
					<h2>Internal error</h2>
					<p>There was an internal error while processing your request. We have been notified of this error and will resolve it shortly. We\'re sorry for the inconvenience.</p>
				</body>
			</html>
		';

		// output
		echo $html;

		// stop script execution
		exit;
	}


	/**
	 * This method will be called by the Spoon Exceptionhandler and is specific for exceptions thrown in JS-files parsed through PHP
	 *
	 * @return	void
	 * @param	object $exception	The exception that was thrown.
	 * @param	string $output		The output that would be mailed.
	 */
	public static function exceptionJSHandler($exception, $output)
	{
		// redefine
		$output = (string) $output;

		// set correct headers
		SpoonHTTP::setHeaders('content-type: application/javascript');

		// output
		echo '// ' . $exception->getMessage();

		// stop script execution
		exit;
	}


	/**
	 * Start session
	 *
	 * @return	void
	 */
	private function initSession()
	{
		SpoonSession::start();
	}


	/**
	 * Require all needed classes
	 *
	 * @return	void
	 */
	private function requireBackendClasses()
	{
		// for specific types, specific files should be loaded
		switch($this->type)
		{
			case 'backend_ajax':
				require_once PATH_WWW . '/routing.php';
			break;
		}
	}


	/**
	 * Require globals-file
	 *
	 * @return	void
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

			// stop script execution
			exit;
		}
	}


	/**
	 * Set debugging
	 *
	 * @return	void
	 */
	private function setDebugging()
	{
		// in debug mode notices are triggered when using non existing locale, so we use a custom errorhandler to cleanup the message
		set_error_handler(array('BackendInit', 'errorHandler'));

		// debugging enabled
		if(SPOON_DEBUG)
		{
			// set error reporting as high as possible
			error_reporting(E_ALL | E_STRICT);

			// show errors on the screen
			ini_set('display_errors', 'On');
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
				break;
			}
		}
	}


	/**
	 * Set include path
	 *
	 * @return	void
	 */
	private function setIncludePath()
	{
		// prepend the libary and document_root to the existing include path
		set_include_path(PATH_LIBRARY . PATH_SEPARATOR . PATH_WWW . PATH_SEPARATOR . get_include_path());
	}
}

?>