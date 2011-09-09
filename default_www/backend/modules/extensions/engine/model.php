<?php

/**
 * In this file we store all generic functions that we will be using in the extensions module.
 *
 * @package		backend
 * @subpackage	extensions
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.6.6
 */
class BackendExtensionsModel
{
	/**
	 * Modules which are part of the core and can not be managed.
	 *
	 * @var	array
	 */
	private static $ignoredModules = array(
		'authentication', 'content_blocks', 'core', 'dashboard',
		'error', 'extensions', 'groups', 'locale', 'pages',
		'search', 'settings', 'tags','users'
	);


	/**
	 * Get installed modules.
	 *
	 * @return	array
	 */
	public static function getModules()
	{
		// get installed modules
		$modules = (array) BackendModel::getDB()->getRecords('SELECT name, description, active FROM modules ORDER BY name ASC');

		// remove ignore modules
		foreach($modules as $i => $module)
		{
			if(in_array($module['name'], self::$ignoredModules)) unset($modules[$i]);
		}

		//  managable modules
		return $modules;
	}
}

?>
