<?php

/**
 * BackendSettingsModel
 *
 * In this file we store all generic functions that we will be using in the SettingsModule
 *
 *
 * @package		backend
 * @subpackage	settings
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendSettingsModel
{
	/**
	 * Fetch the list of modules that require akismet
	 *
	 * @return	array
	 */
	public static function getModulesThatRequireAkismet()
	{
		// init vars
		$modules = array();
		$activeModules = BackendModel::getModules(true);

		// loop active modules
		foreach($activeModules as $module)
		{
			// fetch setting
			$setting = BackendModel::getSetting($module, 'requires_akismet', false);

			// add to the list
			if($setting) $modules[] = $module;
		}

		// final list
		return $modules;
	}


	/**
	 * Fetch the list of modules that require google maps
	 *
	 * @return	array
	 */
	public static function getModulesThatRequireGoogleMaps()
	{
		// init vars
		$modules = array();
		$activeModules = BackendModel::getModules(true);

		// loop active modules
		foreach($activeModules as $module)
		{
			// fetch setting
			$setting = BackendModel::getSetting($module, 'requires_google_maps', false);

			// add to the list
			if($setting) $modules[] = $module;
		}

		// final list
		return $modules;
	}
}

?>