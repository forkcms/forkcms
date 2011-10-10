<?php

/**
 * In this file we store all generic functions that we will be using in the location module
 *
 * @package		frontend
 * @subpackage	location
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.1
 */
class FrontendLocationModel
{
	/**
	 * Get an item
	 *
	 * @return	array
	 * @param	int $id					The id of the item to fetch.
	 */
	public static function get($id)
	{
		return (array) FrontendModel::getDB()->getRecord('SELECT *
															FROM location
															WHERE id = ? AND language = ?',
															array((int) $id, FRONTEND_LANGUAGE));
	}


	/**
	 * Get all items
	 *
	 * @return	array
	 */
	public static function getAll()
	{
		return (array) FrontendModel::getDB()->getRecords('SELECT *
															FROM location
															WHERE language = ?',
															array(FRONTEND_LANGUAGE));
	}
}

?>