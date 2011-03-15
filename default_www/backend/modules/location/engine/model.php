<?php

/**
 * In this file we store all generic functions that we will be using in the location module
 *
 * @package		backend
 * @subpackage	location
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.1
 */
class BackendLocationModel
{
	/**
	 * Overview of all locations
	 *
	 * @var	string
	 */
	const QRY_DATAGRID_BROWSE = 'SELECT id, title, CONCAT(street, " ", number, ", ", zip, " ", city, ", ", country) AS address
									FROM location
									WHERE language = ?';


	/**
	 * Delete an item
	 *
	 * @return	void
	 * @param	int $id						The id of the record to delete.
	 */
	public static function delete($id)
	{
		// get db
		$db = BackendModel::getDB(true);

		// get item
		$item = self::get($id);

		// build extra
		$extra = array('id' => $item['extra_id'],
						'module' => 'location',
						'type' => 'widget',
						'action' => 'location');

		// delete extra
		$db->delete('pages_extras', 'id = ? AND module = ? AND type = ? AND action = ?', array($extra['id'], $extra['module'], $extra['type'], $extra['action']));

		// delete item
		$db->delete('location', 'id = ? AND language = ?', array((int) $id, BL::getWorkingLanguage()));
	}


	/**
	 * Check if an item exists
	 *
	 * @return	bool
	 * @param	int $id						The id of the record to look for.
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT COUNT(i.id)
														FROM location AS i
														WHERE i.id = ? AND i.language = ?',
														array((int) $id, BL::getWorkingLanguage()));
	}


	/**
	 * Fetch a record from the database
	 *
	 * @return	array
	 * @param	int $id						The id of the record to fetch.
	 */
	public static function get($id)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.*
															FROM location AS i
															WHERE i.id = ? AND i.language = ?',
															array((int) $id, BL::getWorkingLanguage()));
	}


	/**
	 * Fetch a record from the database
	 *
	 * @return	array
	 */
	public static function getAll()
	{
		return (array) BackendModel::getDB()->getRecords('SELECT i.*
															FROM location AS i
															WHERE i.language = ?',
															array(BL::getWorkingLanguage()));
	}


	/**
	 * Insert an item
	 *
	 * @return	int
	 * @param	array $item					The data of the record to insert.
	 */
	public static function insert($item)
	{
		// get db
		$db = BackendModel::getDB(true);

		// build extra
		$extra = array('module' => 'location',
						'type' => 'widget',
						'label' => 'Location',
						'action' => 'location',
						'data' => null,
						'hidden' => 'N',
						'sequence' => $db->getVar('SELECT MAX(i.sequence) + 1
													FROM pages_extras AS i
													WHERE i.module = ?', array('location')));
		if(is_null($extra['sequence'])) $extra['sequence'] = $db->getVar('SELECT CEILING(MAX(i.sequence) / 1000) * 1000
																			FROM pages_extras AS i');

		// insert extra
		$item['extra_id'] = $db->insert('pages_extras', $extra);
		$extra['id'] = $item['extra_id'];

		// insert and return the new id
		$item['id'] = $db->insert('location', $item);

		// update extra (item id is now known)
		$extra['data'] = serialize(array('id' => $item['id'],
											'extra_label' => ucfirst(BL::lbl('Location', 'core')) . ': ' . $item['title'],
											'language' => $item['language'],
											'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $item['id']));
		$db->update('pages_extras', $extra, 'id = ? AND module = ? AND type = ? AND action = ?', array($extra['id'], $extra['module'], $extra['type'], $extra['action']));

		// return the new id
		return $item['id'];
	}


	/**
	 * Update an item
	 *
	 * @return	int
	 * @param	array $item					The data of the record to update.
	 */
	public static function update($item)
	{
		// get db
		$db = BackendModel::getDB(true);

		// build extra
		$extra = array('id' => $item['extra_id'],
						'module' => 'location',
						'type' => 'widget',
						'label' => 'Location',
						'action' => 'location',
						'data' => serialize(array('id' => $item['id'],
													'extra_label' => ucfirst(BL::lbl('Location', 'core')) . ': ' . $item['title'],
													'language' => $item['language'],
													'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $item['id'])),
						'hidden' => 'N');

		// update extra
		$db->update('pages_extras', $extra, 'id = ? AND module = ? AND type = ? AND action = ?', array($extra['id'], $extra['module'], $extra['type'], $extra['action']));

		// update item
		return $db->update('location', $item, 'id = ? AND language = ?', array($item['id'], $item['language']));
	}
}

?>