<?php

/**
 * In this file we store all generic functions that we will be using in the extensions module.
 *
 * @package		backend
 * @subpackage	extensions
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		3.0.0
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
	 * Get modules based on the directory listing in the backend application.
	 *
	 * If a module contains a info.xml it will be parsed.
	 *
	 * @return	array
	 */
	public static function getModules()
	{
		// get installed modules
		$installedModules = (array) BackendModel::getDB()->getRecords('SELECT name, active FROM modules', null, 'name');

		// get modules present on the filesystem
		$modules = SpoonDirectory::getList(BACKEND_MODULES_PATH);

		// all modules that are managable in the backend
		$managableModules = array();

		// get more information for each module
		foreach($modules as $moduleName)
		{
			// skip ignored modules
			if(in_array($moduleName, self::$ignoredModules)) continue;

			// init module information
			$module = array();
			$module['id'] = 'module_' . $moduleName;
			$module['raw_name'] = $moduleName;
			$module['name'] = ucfirst(BL::getLabel(SpoonFilter::toCamelCase($moduleName)));
			$module['description'] = '';
			$module['version'] = '';
			$module['active'] = false;
			$module['installed'] = false;

			// the module is present in the database, that means its installed
			if(isset($installedModules[$moduleName]))
			{
				$module['installed'] = true;
				$module['active'] = ($installedModules[$moduleName]['active'] == 'Y');
			}

			// get extra info from the info.xml
			$infoXml = @simplexml_load_file(BACKEND_MODULES_PATH . '/' . $module['name'] . '/info.xml', null, LIBXML_NOCDATA);

			// we need a valid XML
			if($infoXml !== false)
			{
				// process XML to a clean array
				$info = self::processInformationXml($infoXml);

				// set fields if they were found in the XML
				if(isset($info['description'])) $module['description'] = BackendDataGridFunctions::truncate($info['description'], 80);
				if(isset($info['version'])) $module['version'] = $info['version'];
			}

			// add to list of managable modules
			$managableModules[] = $module;
		}

		// managable modules
		return $managableModules;
	}


	/**
	 * Checks if a module is already installed.
	 *
	 * @return	bool
	 * @param	string $module
	 */
	public static function isInstalled($module)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT COUNT(name) FROM modules WHERE name = ?', (string) $module);
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

			// build event information and add it to the list
			$information['events'][] = array(
				'application' => (isset($attributes['application'])) ? $attributes['application'] : '',
				'name' => (isset($attributes['name'])) ? $attributes['name'] : '',
				'description' => $event[0]
			);
		}

		// information array
		return $information;
	}
}

?>
