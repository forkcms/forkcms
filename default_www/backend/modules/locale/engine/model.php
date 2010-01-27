<?php

/**
 * BackendLocaleModel
 *
 * In this file we store all generic functions that we will be using in the locale module
 *
 *
 * @package		backend
 * @subpackage	locale
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendLocaleModel
{
	const QRY_DATAGRID_BROWSE = 'SELECT * FROM locale AS l WHERE l.language = ?';


	public static function buildCache($language)
	{
		// rebuild the language file
	}

	public static function exists($id)
	{
		// get db
		$db = BackendModel::getDB();

		// exists?
		return $db->getNumRows('SELECT id FROM locale WHERE id = ?;', (int) $id);
	}


	public static function get($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = BackendModel::getDB();

		// get record and return it
		return (array) $db->getRecord('SELECT * FROM locale WHERE id = ?;', (int) $id);
	}


	public static function getTypesForDropDown()
	{
		// get db
		$db = BackendModel::getDB();

		// init var
		$dropdown = array();

		// fetch types
		$types = BackendModel::getDB()->getEnumValues('locale', 'type');

		// add types
		foreach($types as $type) $dropdown[$type] = $type;

		// get data
		return $dropdown;
	}

	public static function update($id, array $item)
	{
		// get db
		$db = BackendModel::getDB();

		// update category
		$db->update('locale', $item, 'id = ?', (int) $id);

		// rebuild the cache
		self::buildCache(BL::getWorkingLanguage());
	}
}

?>