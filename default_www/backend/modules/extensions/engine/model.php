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
	 * Does this module exist.
	 * This does not check for existence in the database but on the filesystem.
	 *
	 * @return	bool
	 * @param	string $module		Module to check for existence.
	 */
	public static function existsModule($module)
	{
		// recast
		$module = (string) $module;

		// check if modules directory exists
		return SpoonDirectory::exists(BACKEND_MODULES_PATH . '/' . $module);
	}


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


	/**
	 * Process the information XML and return an array with the information.
	 *
	 * @return	array
	 * @param	SimpleXMLElement $xml
	 */
	public static function processInformationXml(SimpleXMLElement $xml)
	{
		// init
		$information = array();

		// version
		$version = $xml->xpath('/module/version/text()');
		if($version !== false) $information['version'] = (string) $version[0];

		// description
		$description = $xml->xpath('/module/description/text()');
		if($description !== false) $information['description'] = (string) $description[0];

		// authors
		foreach($xml->xpath('/module/authors/author') as $author)
		{
			$information['authors'][] = (array) $author;
		}

		// events
		foreach($xml->xpath('/module/events/event') as $event)
		{
			// lose the simplexmlelement
			$event = (array) $event;

			// attributes
			$attributes = isset($event['@attributes']) ? (array) $event['@attributes'] : array();

			//
			$tmp['application'] = (isset($attributes['application'])) ? $attributes['application'] : '';
			$tmp['name'] = (isset($attributes['name'])) ? $attributes['name'] : '';
			$tmp['description'] = $event[0];

			$information['events'][] = (array) $tmp;
		}

		// information array
		return $information;
	}
}

?>
