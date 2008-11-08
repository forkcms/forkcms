<?php

/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package			frontend
 * @subpackage		core
 *
 * @author 			Tijs Verkoyen <tijs@netlash.com>
 * @since			2.0
 */
class CoreModel
{
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


	// @todo documenteren
	public static function getPageRecordByMenuId($menuId)
	{
		// redefine
		$menuId = (int) $menuId;

		// get database instance
		$db = self::getDB();

		// get data
		$record = (array) $db->getRecord('SELECT p.menu_id, p.extra_id, p.template_id, p.title, p.content, p.navigation_title, p.navigation_title_overwrite,
												m.pagetitle AS meta_pagetitle, m.pagetitle_overwrite AS meta_pagetitle_overwrite,
												m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
												m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
												m.custom AS meta_custom,
												m.url, m.url_overwrite,
												e.location AS extra_location, e.module_id AS extra_module_id, e.parameters AS extra_parameters,
												t.location AS template_location
											FROM pages AS p
											INNER JOIN meta AS m ON p.meta_id = m.id
											LEFT OUTER JOIN pages_extra AS e ON p.extra_id = e.id
											LEFT OUTER JOIN pages_templates AS t ON p.template_id = t.id
											WHERE p.menu_id = ? AND p.active = ? AND p.hidden = ? AND p.language = ?
											LIMIT 1;',
											array((int) $menuId, 'Y', 'N', FRONTEND_LANGUAGE));

		// unserialize parameters
		if(isset($record['extra_parameters']) && $record['extra_parameters'] != '')
		{
			$record['extra_parameters'] = unserialize($record['extra_parameters']);
		}

		// return
		return (array) $record;
	}
}

?>