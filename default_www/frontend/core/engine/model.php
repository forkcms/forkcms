<?php

/**
 * FrontendModel
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendModel
{
	/**
	 * cached modules
	 *
	 * @var	array
	 */
	private static $modules = array();


	/**
	 * cached module-settings
	 *
	 * @var	array
	 */
	private static $moduleSettings = array();


	/**
	 * Get (or create and get) a database-connection
	 * @later: we should extend SpoonDatabase with FrontendDatabas, which will enable us to split read and write-connections.
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

			// utf8 compliance
			$db->execute('SET CHARACTER SET utf8;');
			$db->execute('SET NAMES utf8;');

			// store
			Spoon::setObjectReference('database', $db);
		}

		return Spoon::getObjectReference('database');
	}


	/**
	 * Get a module setting
	 *
	 * @return	mixed
	 * @param	string $module
	 * @param	string $name
	 * @param	mixed[optional] $defaultValue
	 */
	public static function getModuleSetting($module, $name, $defaultValue = null)
	{
		// redefine
		$module = (string) $module;
		$name = (string) $name;

		// get them all
		if(empty(self::$moduleSettings))
		{
			// get db
			$db = self::getDB();

			// fetch settings
			$settings = (array) $db->retrieve('SELECT ms.module, ms.name, ms.value
												FROM modules_settings AS ms;');

			// loop settings and cache them, also unserialize the values
			foreach($settings as $row) self::$moduleSettings[$row['module']][$row['name']] = unserialize($row['value']);
		}

		// if the setting doesn't exists, store it (it will be available from te cache)
		if(!isset(self::$moduleSettings[$module][$name])) self::setModuleSetting($module, $name, $defaultValue);

		// return
		return self::$moduleSettings[$module][$name];
	}


	/**
	 * Get all module settings at once
	 *
	 * @return	array
	 * @param	string $module
	 */
	public static function getModuleSettings($module)
	{
		// redefine
		$module = (string) $module;

		// get them all
		if(empty(self::$moduleSettings[$module]))
		{
			// get db
			$db = self::getDB();

			// fetch settings
			$settings = (array) $db->retrieve('SELECT ms.module, ms.name, ms.value
												FROM modules_settings AS ms;');

			// loop settings and cache them, also unserialize the values
			foreach($settings as $row) self::$moduleSettings[$row['module']][$row['name']] = unserialize($row['value']);
		}

		// validate again
		if(!isset(self::$moduleSettings[$module])) return array();

		// return
		return self::$moduleSettings[$module];
	}


	/**
	 * Get all data for a page
	 *
	 * @return	array
	 * @param	int $pageId
	 */
	public static function getPage($pageId)
	{
		// redefine
		$pageId = (int) $pageId;

		// get database instance
		$db = self::getDB();

		// get data
		$record = (array) $db->getRecord('SELECT p.id, p.revision_id, p.template_id, p.title, p.navigation_title, p.navigation_title_overwrite, p.data,
												m.title AS meta_title, m.title_overwrite AS meta_title_overwrite,
												m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
												m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
												m.custom AS meta_custom,
												m.url, m.url_overwrite,
												t.path AS template_path, t.data as template_data
											FROM pages AS p
											INNER JOIN meta AS m ON p.meta_id = m.id
											INNER JOIN pages_templates AS t ON p.template_id = t.id
											WHERE p.id = ? AND p.status = ? AND p.hidden = ? AND p.language = ?
											LIMIT 1;',
											array($pageId, 'active', 'N', FRONTEND_LANGUAGE));

		// validate
		if(empty($record)) return array();

		// unserialize page data and template data
		if(isset($record['data']) && $record['data'] != '') $record['data'] = unserialize($record['data']);
		if(isset($record['template_data']) && $record['template_data'] != '') $record['template_data'] = unserialize($record['template_data']);

		// get blocks
		$record['blocks'] = (array) $db->retrieve('SELECT pb.extra_id, pb.html,
													pe.module AS extra_module, pe.type AS extra_type, pe.action AS extra_action, pe.data AS extra_data
													FROM pages_blocks AS pb
													LEFT OUTER JOIN pages_extras AS pe ON pb.extra_id = pe.id
													WHERE pb.revision_id = ? AND pb.status = ?;',
													array($record['revision_id'], 'active'));

		// loop blocks
		foreach($record['blocks'] as $index => $row)
		{
			// unserialize data if it is available
			if(isset($row['data'])) $record['blocks'][$index]['data'] = unserialize($row['data']);
		}

		return $record;
	}


	/**
	 * General method to check if something is spam
	 *
	 * @return	bool
	 * @param	string $content
	 * @param	string $permaLink
	 * @param	string[optional] $author
	 * @param	string[optional] $email
	 * @param	string[optional] $URL
	 * @param	string[optional] $type
	 */
	public static function isSpam($content, $permaLink, $author = null, $email = null, $URL = null, $type = 'comment')
	{
		// get some settings
		$akismetKey = self::getModuleSetting('core', 'akismet_key');

		// invalid key, so we can't detect spam
		if($akismetKey === '') return false;

		// require the class
		require_once PATH_LIBRARY .'/external/akismet.php';

		// create new instance
		$akismet = new Akismet($akismetKey, SITE_URL);

		// set properties
		$akismet->setTimeOut(10);
		$akismet->setUserAgent('Fork CMS/2.0');

		// try it to decide it the item is spam
		try
		{
			// check with Akismet if the item is spam
			return $akismet->isSpam($content, $author, $email, $URL, $permaLink, $type);
		}

		// catch exceptions
		catch(Exception $e)
		{
			// in debug mode we want to see exceptions, otherwise the fallback will be triggered
			if(SPOON_DEBUG) throw $e;
		}

		// when everything fails
		return false;
	}


	/**
	 * Store a modulesetting
	 *
	 * @return	void
	 * @param	string $module
	 * @param	string $name
	 * @param	mixed $value
	 */
	public static function setModuleSetting($module, $name, $value)
	{
		// redefine
		$module = (string) $module;
		$name = (string) $name;
		$value = serialize($value);

		// get db
		$db = self::getDB();

		// store
		$db->execute('INSERT INTO modules_settings (module, name, value)
						VALUES (?, ?, ?)
						ON DUPLICATE KEY UPDATE value = ?;',
						array($module, $name, $value, $value));

		// store in cache
		self::$moduleSettings[$module][$name] = unserialize($value);
	}
}

?>