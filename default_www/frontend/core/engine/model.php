<?php

/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class CoreModel
{
	/**
	 * Cached module-settings
	 *
	 * @var	array
	 */
	private static $aModuleSettings = array();


	/**
	 * Get (or create and get) a database-connection
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


	/**
	 * Get a module settings
	 *
	 * @return	mixed
	 * @param	string $moduleName
	 * @param	string $setting
	 * @param	mixed[optional] $defaultValue
	 */
	public static function getModuleSetting($moduleName, $settingName, $defaultValue = null)
	{
		// redefine
		$moduleName = (string) $moduleName;
		$settingName = (string) $settingName;

		// get db
		$db = self::getDB();

		// get them all
		if(!empty(self::$aModuleSettings))
		{
			$aSettings = (array) $db->retrieve('SELECT m.name AS module_name, s.name, s.value
												FROM modules_settings AS s
												INNER JOIN modules AS m ON s.module_id = m.id;');

			// loop settings and cache them, also unserialize the values
			foreach($aSettings as $row) self::$aModuleSettings[$row['module_name']][$row['name']] = unserialize($row['value']);
		}

		// if the setting doesn't exists, store it
		if(!isset(self::$aModuleSettings[$moduleName][$settingName])) self::setModuleSetting($moduleName, $settingName, $defaultValue);

		// return
		return self::$aModuleSettings[$moduleName][$settingName];
	}


	/**
	 * Get all data for a page
	 *
	 * @return	array
	 * @param	int $pageId
	 */
	public static function getPageRecordByPageId($pageId)
	{
		// redefine
		$pageId = (int) $pageId;

		// get database instance
		$db = self::getDB();

		// get data
		$record = (array) $db->getRecord('SELECT p.page_id, p.extra_id, p.template_id, p.title, p.content, p.navigation_title, p.navigation_title_overwrite,
												m.pagetitle AS meta_pagetitle, m.pagetitle_overwrite AS meta_pagetitle_overwrite,
												m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
												m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
												m.custom AS meta_custom,
												m.url, m.url_overwrite,
												md.name AS extra_module, e.action AS extra_action, e.parameters AS extra_parameters,
												t.location AS template_location
											FROM pages AS p
											INNER JOIN meta AS m ON p.meta_id = m.id
											LEFT OUTER JOIN pages_extras AS e ON p.extra_id = e.id
											LEFT OUTER JOIN modules AS md ON e.module_id = md.id
											LEFT OUTER JOIN pages_templates AS t ON p.template_id = t.id
											WHERE p.page_id = ? AND p.active = ? AND p.hidden = ? AND p.language = ?
											LIMIT 1;',
											array((int) $pageId, 'Y', 'N', FRONTEND_LANGUAGE));

		// unserialize parameters
		if(isset($record['extra_parameters']) && $record['extra_parameters'] != '') $record['extra_parameters'] = unserialize($record['extra_parameters']);

		// return
		return (array) $record;
	}


	/**
	 * Store a modulesetting
	 *
	 * @return	void
	 * @param	string $moduleName
	 * @param	string $settingName
	 * @param	mixed $value
	 */
	public static function setModuleSetting($moduleName, $settingName, $value)
	{
		// redefine
		$moduleName = (string) $moduleName;
		$settingName = (string) $settingName;
		$value = serialize($value);

		// get db
		$db = self::getDB();

		// get module id
		$moduleId = $db->getVar('SELECT m.id
										FROM modules AS m
										WHERE m.name = ?
										LIMIT 1;',
										array($moduleName));

		// validate module
		if($moduleId === null) throw new FrontendException('Invalid module ('. $moduleName .').');

		// redefine
		$moduleId = (int) $moduleId;

		// store
		$db->execute('INSERT INTO modules_settings (module_id, name, value)
						VALUES (?, ?, ?)
						ON DUPLICATE KEY UPDATE value = ?;',
						array($moduleId, $settingName, $value, $value));

		// store in cache
		self::$aModuleSettings[$moduleName][$settingName] = unserialize($value);
	}
}

?>