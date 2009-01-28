<?php

/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package			frontend
 *
 * @author 			Tijs Verkoyen <tijs@netlash.com>
 * @since			2.0
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
		$allowedTypes = array('backend');
		$type = (string) $type;

		// check if this is a valid type
		if(!in_array($type, $allowedTypes)) exit('Invalid init-type');

		// set type
		$this->type = $type;

		// set some ini-options
		ini_set('memory_limit', '64M');

		// require globals
		$this->requireGlobals();

		// define constants
		$this->definePaths();
		$this->defineUrls();

		// set include path
		$this->setIncludePath();

		// set debugging
		$this->setDebugging();

		// require spoon-classes
		$this->requireSpoonClasses();

		// require frontend-classes
		$this->requireBackendClasses();

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
		// general paths
		define('BACKEND_PATH', PATH_WWW .'/'. APPLICATION);
		define('BACKEND_CACHE_PATH', BACKEND_PATH .'/cache');
		define('BACKEND_CORE_PATH', BACKEND_PATH .'/core');
		define('BACKEND_MODULES_PATH', BACKEND_PATH .'/modules');

		define('FRONTEND_PATH', PATH_WWW .'/frontend');
		define('FRONTEND_CACHE_PATH', FRONTEND_PATH .'/cache');
		define('FRONTEND_CORE_PATH', FRONTEND_PATH .'/core');
		define('FRONTEND_MODULES_PATH', FRONTEND_PATH .'/modules');
	}


	/**
	 * Define urls
	 *
	 * @return	void
	 */
	private function defineUrls()
	{
		define('BACKEND_CORE_URL', '/backend/core');
		define('BACKEND_CACHE_URL', '/backend/cache');
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
		// general classes
		require_once BACKEND_CORE_PATH .'/engine/exception.php';
		require_once BACKEND_CORE_PATH .'/engine/authentication.php';
		require_once BACKEND_CORE_PATH .'/engine/language.php';
		require_once BACKEND_CORE_PATH .'/engine/url.php';
		require_once BACKEND_CORE_PATH .'/engine/template.php';
		require_once BACKEND_CORE_PATH .'/engine/header.php';
		require_once BACKEND_CORE_PATH .'/engine/navigation.php';
		require_once BACKEND_CORE_PATH .'/engine/base_config.php';
		require_once BACKEND_CORE_PATH .'/engine/base_action.php';
		require_once BACKEND_CORE_PATH .'/engine/action.php';
		require_once BACKEND_CORE_PATH .'/engine/datagrid.php';

		// frontend
		require FRONTEND_CORE_PATH .'/engine/language.php';
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
			// default
			default:
				require_once '../library/globals.php';
				require_once '../library/globals_backend.php';
		}

	}


	/**
	 * Require all needed Spoon classes
	 *
	 * @return	void
	 */
	private function requireSpoonClasses()
	{
		require_once 'spoon/session/session.php';
		require_once 'spoon/database/database.php';
		require_once 'spoon/cookie/cookie.php';
		require_once 'spoon/http/http.php';
		require_once 'spoon/template/template.php';
		require_once 'spoon/html/datagrid/datagrid.php';
		require_once 'spoon/html/form/form.php';
	}


	/**
	 * Set debugging
	 *
	 * @return	void
	 */
	private function setDebugging()
	{
		if(SPOON_DEBUG)
		{
			error_reporting(E_ALL | E_STRICT);
			ini_set('display_errors', 'On');
		}
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