<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the location module
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class FrontendLocationModel
{
	/**
	 * Get an item
	 *
	 * @param int $id The id of the item to fetch.
	 * @return array
	 */
	public static function get($id)
	{
		return (array) FrontendModel::getDB()->getRecord(
			'SELECT *
			 FROM location
			 WHERE id = ? AND language = ?',
			array((int) $id, FRONTEND_LANGUAGE)
		);
	}

	/**
	 * Get all items
	 *
	 * @return array
	 */
	public static function getAll()
	{
		return (array) FrontendModel::getDB()->getRecords(
			'SELECT * FROM location WHERE language = ?',
			array(FRONTEND_LANGUAGE)
		);
	}
}
