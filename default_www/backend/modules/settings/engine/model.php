<?php

/**
 * In this file we store all generic functions that we will be using in the settings module.
 *
 * @package		backend
 * @subpackage	settings
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class BackendSettingsModel
{
	/**
	 * Fetch the list of modules that require Akismet API key
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
			$setting = BackendModel::getModuleSetting($module, 'requires_akismet', false);

			// add to the list
			if($setting) $modules[] = $module;
		}

		// return
		return $modules;
	}


	/**
	 * Fetch the list of modules that require Google Maps API key
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
			$setting = BackendModel::getModuleSetting($module, 'requires_google_maps', false);

			// add to the list
			if($setting) $modules[] = $module;
		}

		// return
		return $modules;
	}


	/**
	 * Get warnings for active modules
	 *
	 * @return	array
	 */
	public static function getWarnings()
	{
		// init vars
		$warnings = array();
		$activeModules = BackendModel::getModules(true);

		// add warnings
		$warnings = array_merge($warnings, BackendModel::checkSettings());

		// loop active modules
		foreach($activeModules as $module)
		{
			// model class
			$class = 'Backend' . SpoonFilter::toCamelCase($module) . 'Model';

			// model file exists
			if(SpoonFile::exists(BACKEND_MODULES_PATH . '/' . $module . '/engine/model.php'))
			{
				// require class
				require_once BACKEND_MODULES_PATH . '/' . $module . '/engine/model.php';
			}

			// method exists
			if(is_callable(array($class, 'checkSettings')))
			{
				// add possible warnings
				$warnings = array_merge($warnings, call_user_func(array($class, 'checkSettings')));
			}
		}

		// return
		return (array) $warnings;
	}
}

?>