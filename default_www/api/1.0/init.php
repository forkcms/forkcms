<?php

/**
 * This class will initiate the API
 *
 * @package		api
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class APIInit
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
		$allowedTypes = array('api');
		$type = (string) $type;

		// check if this is a valid type
		if(!in_array($type, $allowedTypes)) exit('Invalid init-type');

		// set type
		$this->type = $type;

		// register the autoloader
		spl_autoload_register(array('APIInit', 'autoLoader'));

		// set some ini-options
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
		$this->requireAPIClasses();

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
		$exceptions = array();
		$exceptions['bl'] = BACKEND_CORE_PATH . '/engine/language.php';
		$exceptions['api'] = API_CORE_PATH . '/1.0/engine/api.php';

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

						// file to be loaded
						$pathToLoad = constant($rootPath) . '/modules/' . $module . '/engine/' . $action . '.php';

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
	}


	/**
	 * Define paths
	 *
	 * @return	void
	 */
	private function definePaths()
	{
		// general paths
		define('API_CORE_PATH', PATH_WWW . '/' . APPLICATION);
		define('BACKEND_PATH', PATH_WWW . '/backend');
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
//		define('BACKEND_CORE_URL', '/backend/core');
//		define('BACKEND_CACHE_URL', '/backend/cache');

//		define('FRONTEND_FILES_URL', '/frontend/files');
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

		// output JSON to the browser
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
		$html = '<html><body>Something went wrong.</body></html>';

		// output HTML to the browser
		echo $html;

		// stop script execution
		exit;
	}


	/**
	 * This method will be called by the Spoon Exceptionhandler and is specific for exceptions thrown in JS-files parsed through PHP
	 *
	 * @return	void
	 * @param	object $exception	The exception that was thrown.
	 * @param	string $output		The output that should be mailed.
	 */
	public static function exceptionJSHandler($exception, $output)
	{
		// redefine
		$output = (string) $output;

		// set correct headers
		SpoonHTTP::setHeaders('content-type: application/javascript');

		// output exception
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
	private function requireAPIClasses()
	{
	}


	/**
	 * Require globals-file
	 *
	 * @return	void
	 */
	private function requireGlobals()
	{
		// fetch config
		$installed[] = @include_once dirname(__FILE__) . '/../../backend/cache/config/config.php';

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
		// debugging enabled
		if(SPOON_DEBUG)
		{
			// set error reporting as high as possible
			error_reporting(E_ALL | E_STRICT);

			// show errors on the screen
			ini_set('display_errors', 'On');

			// in debug mode notices are triggered when using non existing locale, so we use a custom errorhandler to cleanup the message
			set_error_handler(array('APIInit', 'errorHandler'));
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