<?php

/**
 * Init
 * This class will initiate the frontend-application
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
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
	 * @param	string $type
	 */
	public function __construct($type)
	{
		// init vars
		$allowedTypes = array('frontend', 'frontend_ajax', 'frontend_js');
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

		/**
		 * At first we enable the error reporting. Later on it will be disabled based on the
		 * value of SPOON_DEBUG, but for now it's required to see possible errors while trying
		 * to include the globals file(s).
		 */
		error_reporting(E_ALL | E_STRICT);
		ini_set('display_errors', 'On');

		// require globals
		$this->requireGlobals();

		// get last modified time for globals
		$lastModifiedTime = @filemtime(PATH_LIBRARY .'/globals.php');

		// reset lastmodified time if needed (SPOON_DEBUG is enabled or we don't get a decent timestamp)
		if($lastModifiedTime === false || SPOON_DEBUG) $lastModifiedTime = time();

		// define as a constant
		define('LAST_MODIFIED_TIME', $lastModifiedTime);

		// define constants
		$this->definePaths();
		$this->defineURLs();

		// set include path
		$this->setIncludePath();

		// set debugging
		$this->setDebugging();

		// require spoon
		require_once 'spoon/spoon.php';

		// require frontend-classes
		$this->requireFrontendClasses();

		// disable magic quotes
		SpoonFilter::disableMagicQuotes();

		// start session
		$this->initSession();
	}


	/**
	 * Autoloader for the frontend
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
		$exceptions['frontend'] = FRONTEND_CORE_PATH .'/engine/frontend.php';
		$exceptions['frontendbaseajaxaction'] = FRONTEND_CORE_PATH .'/engine/ajax.php';
		$exceptions['frontendbaseconfig'] = FRONTEND_CORE_PATH .'/engine/base.php';
		$exceptions['frontendbaseobject'] = FRONTEND_CORE_PATH .'/engine/base.php';
		$exceptions['frontendblockextra'] = FRONTEND_CORE_PATH .'/engine/block.php';
		$exceptions['frontendblockwidget'] = FRONTEND_CORE_PATH .'/engine/block.php';

		// is it an exception
		if(isset($exceptions[$className])) $pathToLoad = $exceptions[$className];

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
		if($this->type == 'frontend_js') define('APPLICATION', 'frontend');
		elseif($this->type == 'frontend_ajax') define('APPLICATION', 'frontend');

		// general paths
		define('FRONTEND_PATH', PATH_WWW .'/'. APPLICATION);
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
		define('FRONTEND_CORE_URL', '/'. APPLICATION .'/core');
		define('FRONTEND_CACHE_URL', '/'. APPLICATION .'/cache');
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
	private function requireFrontendClasses()
	{
		// based on the type
		switch($this->type)
		{
			case 'frontend':
				require_once FRONTEND_CORE_PATH .'/engine/template_custom.php';
				require_once FRONTEND_PATH .'/modules/tags/engine/model.php';
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
		// based on the type
		switch($this->type)
		{
			case 'frontend_ajax':
			case 'frontend_js':
				require_once '../../library/globals.php';
				require_once '../../library/globals_frontend.php';
			break;

			// default
			default:
				require_once '../library/globals.php';
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
			// set error reporting as high as possible
			error_reporting(E_ALL | E_STRICT);

			// show on screen
			ini_set('display_errors', 'On');
		}

		// debugging disabled
		else
		{
			// set error reporting as low as possible
			error_reporting(0);

			// don't show errors on screen
			ini_set('display_errors', 'Off');
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