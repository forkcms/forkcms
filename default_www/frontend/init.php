<?php

/**
 * Init
 *
 * This class will initiate the frontend-application
 *
 * @package		frontend
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
	 * @param	string $type
	 * @return	void
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

		// require spoon-classes
		$this->requireSpoonClasses();

		// require frontend-classes
		$this->requireFrontendClasses();

		// disable magic quotes
		SpoonFilter::disableMagicQuotes();

		// start session
		$this->initSession();
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
		if($this->type == 'frontend_ajax') define('APPLICATION', 'frontend');

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
		// @todo check
		require_once FRONTEND_CORE_PATH .'/engine/base_object.php';

		// general classes
		require_once FRONTEND_CORE_PATH .'/engine/exception.php';
		require_once FRONTEND_CORE_PATH .'/engine/template.php';
		require_once FRONTEND_CORE_PATH .'/engine/language.php';
		require_once FRONTEND_CORE_PATH .'/engine/model.php';
		require_once FRONTEND_CORE_PATH .'/engine/url.php';
		require_once FRONTEND_CORE_PATH .'/engine/navigation.php';

		// based on the type
		switch ($this->type)
		{
			case 'frontend':
				require_once FRONTEND_CORE_PATH .'/engine/frontend.php';
				require_once FRONTEND_CORE_PATH .'/engine/page.php';
				require_once FRONTEND_CORE_PATH .'/engine/header.php';
				require_once FRONTEND_CORE_PATH .'/engine/breadcrumb.php';
				require_once FRONTEND_CORE_PATH .'/engine/navigation.php';
				require_once FRONTEND_CORE_PATH .'/engine/footer.php';
				require_once FRONTEND_PATH .'/modules/tags/engine/model.php';
			break;

			case 'frontend_ajax':
				require_once FRONTEND_CORE_PATH .'/engine/ajax.php';
				require_once FRONTEND_CORE_PATH .'/engine/base_ajax_action.php';
				require_once FRONTEND_CORE_PATH .'/engine/ajax_action.php';
			break;

			case 'frontend_js':
				require_once FRONTEND_CORE_PATH .'/engine/javascript.php';
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
				require_once '../../library/globals_backend.php';
			break;

			// default
			default:
				require_once '../library/globals.php';
				require_once '../library/globals_frontend.php';
		}

	}


	/**
	 * Require all needed Spoon classes
	 *
	 * @return	void
	 */
	private function requireSpoonClasses()
	{
		require_once 'spoon/spoon.php';
		require_once 'spoon/locale/locale.php';
		require_once 'spoon/session/session.php';
		require_once 'spoon/database/database.php';
		require_once 'spoon/cookie/cookie.php';
		require_once 'spoon/http/http.php';
		require_once 'spoon/template/template.php';

		switch($this->type)
		{
			case 'frontend':
				require_once 'spoon/html/datagrid/datagrid.php';
				require_once 'spoon/html/form/form.php';
				require_once 'spoon/image/thumbnail.php';
			break;
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