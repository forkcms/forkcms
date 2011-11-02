<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the settings module.
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendSettingsModel
{
	/**
	 * Fetch the list of modules that require Akismet API key
	 *
	 * @return array
	 */
	public static function getModulesThatRequireAkismet()
	{
		// init vars
		$modules = array();
		$installedModules = BackendModel::getModules();

		// loop modules
		foreach($installedModules as $module)
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
	 * @return array
	 */
	public static function getModulesThatRequireGoogleMaps()
	{
		// init vars
		$modules = array();
		$installedModules = BackendModel::getModules();

		// loop modules
		foreach($installedModules as $module)
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
	 * @return array
	 */
	public static function getWarnings()
	{
		// init vars
		$warnings = array();
		$installedModules = BackendModel::getModules();

		// add warnings
		$warnings = array_merge($warnings, BackendModel::checkSettings());

		// loop modules
		foreach($installedModules as $module)
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

		return (array) $warnings;
	}
}
