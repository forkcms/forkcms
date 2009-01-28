<?php

/** Require Model */
require_once BACKEND_CORE_PATH .'/engine/model.php';

/**
 * Backend
 *
 * This class defines the backend, it is the core. Everything starts here.
 * We create all needed instances and execute the requested action
 *
 * @package		backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class Backend
{
	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// create url-object to handle the url
		$url = new BackendURL();

		// create new template so we have a reference that will be available on every module/action
		new BackendTemplate();

		// create a header-object
		new BackendHeader();

		// create a navigation object
		new BackendNavigation();

		// create a new action
		$action = new BackendAction($url->getAction(), $url->getModule());

		// execute the action
		$action->execute();
	}
}

?>