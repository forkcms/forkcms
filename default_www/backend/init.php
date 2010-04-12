<?php

/**
 * Init
 * This class will initiate the backend-application
 *
 * @package		backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class Init
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
	 * @param	string $type	The type of init to load, possible values are: backend, backend_ajax, backend_js.
	 */
	public function __construct($type)
	{
		// init vars
		$allowedTypes = array('backend', 'backend_ajax', 'backend_js', 'backend_cronjob');
		$type = (string) $type;

		// check if this is a valid type
		if(!in_array($type, $allowedTypes)) exit('Invalid init-type');

		// set type
		$this->type = $type;

		// register the autoloader
		spl_autoload_register(array('Init', 'autoLoader'));

		// set some ini-options
		ini_set('memory_limit', '64M');

		// set a default timezone if no one was set by PHP.ini
		if(ini_get('date.timezone') == '') date_default_timezone_set('Europe/Brussels');

		/*
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
	 * @param	string $className	The name of the class to require
	 */
	public static function autoLoader($className)
	{
		// redefine
		$className = strtolower((string) $className);

		// init var
		$pathToLoad = '';

		// exceptions
		$exceptions = array();
		$exceptions['backend'] = BACKEND_CORE_PATH .'/engine/backend.php';
		$exceptions['backendajaxaction'] = BACKEND_CORE_PATH .'/engine/ajax_action.php';
		$exceptions['backenddatagriddb'] = BACKEND_CORE_PATH .'/engine/datagrid.php';
		$exceptions['backendbaseconfig'] = BACKEND_CORE_PATH .'/engine/base.php';
		$exceptions['backendbasecronjob'] = BACKEND_CORE_PATH .'/engine/base.php';

		// is it an exception
		if(isset($exceptions[$className])) $pathToLoad = $exceptions[$className];

		// backend
		elseif(substr($className, 0, 7) == 'backend') $pathToLoad = BACKEND_CORE_PATH .'/engine/'. str_replace('backend', '', $className) .'.php';

		// frontend
		elseif(substr($className, 0, 8) == 'frontend') $pathToLoad = FRONTEND_CORE_PATH .'/engine/'. str_replace('frontend', '', $className) .'.php';

		// file check in core
		if($pathToLoad != '' && SpoonFile::exists($pathToLoad)) require_once $pathToLoad;
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
		define('BACKEND_PATH', PATH_WWW .'/'. APPLICATION);
		define('BACKEND_CACHE_PATH', BACKEND_PATH .'/cache');
		define('BACKEND_CORE_PATH', BACKEND_PATH .'/core');
		define('BACKEND_MODULES_PATH', BACKEND_PATH .'/modules');

		define('FRONTEND_PATH', PATH_WWW .'/frontend');
		define('FRONTEND_CACHE_PATH', FRONTEND_PATH .'/cache');
		define('FRONTEND_CORE_PATH', FRONTEND_PATH .'/core');
		define('FRONTEND_MODULES_PATH', FRONTEND_PATH .'/modules');
		define('FRONTEND_FILES_PATH', FRONTEND_PATH .'/files');
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
			case 'backend':
				require_once BACKEND_PATH .'/modules/tags/engine/model.php';
				require_once BACKEND_PATH .'/modules/users/engine/model.php';
			break;

			case 'backend_ajax':
				require_once PATH_WWW .'/routing.php';
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
		switch($this->type)
		{
			case 'backend_ajax':
			case 'backend_cronjob':
			case 'backend_js':
				require_once '../../library/globals.php';
				require_once '../../library/globals_backend.php';
				require_once '../../library/globals_frontend.php';
			break;

			// default
			default:
				require_once '../library/globals.php';
				require_once '../library/globals_backend.php';
				require_once '../library/globals_frontend.php';
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
			error_reporting(E_ALL | E_STRICT);
			ini_set('display_errors', 'On');
		}

		// debugging disabled
		else
		{
			error_reporting(0);
			ini_set('display_errors', 'Off');
		}
	}


	/**
	 * Set includepath
	 *
	 * @return	void
	 */
	private function setIncludePath()
	{
		set_include_path(PATH_LIBRARY . PATH_SEPARATOR . PATH_WWW . PATH_SEPARATOR . get_include_path());
	}
}

?>