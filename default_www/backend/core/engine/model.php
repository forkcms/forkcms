<?php

/**
 * BackendModel
 *
 * In this file we store all generic functions that we will be using in the backend.
 *
 *
 * @package		backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendModel
{
	/**
	 * Creates an url for a given action and module
	 * If you don't specify an action the current action will be used
	 * If you don't specify a module the current module will be used
	 * If you don't specify a language the current language will be used
	 *
	 * @return	string
	 * @param	string[optional] $action
	 * @param	string[optiona] $module
	 * @param	string[optiona] $language
	 */
	public static function createURLForAction($action = null, $module = null, $language = null)
	{
		// grab the url from the reference
		$url = Spoon::getObjectReference('url');

		// redefine parameters
		$action = ($action !== null) ? (string) $action : $url->getAction();
		$module = ($module !== null) ? (string) $module : $url->getModule();
		$language = ($language !== null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// build the url and return it
		return '/'. NAMED_APPLICATION .'/'. $language .'/'. $module .'/'. $action;
	}


	/**
	 * Get (or create and get) a database-connection
	 * If the database wasn't stored in teh reference before we will create it and add it
	 *
	 * @return	SpoonDatabase
	 */
	public static function getDB()
	{
		// do we have a db-object ready?
		if(!Spoon::isObjectReference('database'))
		{
			// create instance
			$db = new SpoonDatabase(DB_TYPE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

			// store
			Spoon::setObjectReference('database', $db);
		}

		// return it
		return Spoon::getObjectReference('database');
	}
}

?>