<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the sitemap module
 *
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class BackendSitemapModel
{
	/**
	 * Delete a certain item
	 *
	 * @param int $id
	 */
	public static function delete($id)
	{
		BackendModel::getDB(true)->delete('sitemap', 'id = ?', (int) $id);
	}

	/**
	 * Checks if a certain item exists
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(i.id)
			 FROM sitemap AS i
			 WHERE i.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Fetches a certain item
	 *
	 * @param int $id
	 * @return array
	 */
	public static function get($id)
	{
		return (array) BackendModel::getDB()->getRecord(
			'SELECT i.*
			 FROM sitemap AS i
			 WHERE i.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Retrieve the unique url for an item
	 *
	 * @param string $url
	 * @param int[optional] $id
	 * @return string
	 */
	public static function getUrl($url, $id = null)
	{
		// redefine Url
		$url = SpoonFilter::urlise((string) $url);

		// get db
		$db = BackendModel::getDB();

		// new item
		if($id === null)
		{
			// get number of categories with this Url
			$number = (int) $db->getVar(
				'SELECT COUNT(i.id)
				 FROM sitemap AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ?',
				array(BL::getWorkingLanguage(), $url));

			// already exists
			if($number != 0)
			{
				// add number
				$url = BackendModel::addNumber($url);

				// try again
				return self::getUrl($url);
			}
		}
		// current category should be excluded
		else
		{
			// get number of items with this Url
			$number = (int) $db->getVar(
				'SELECT COUNT(i.id)
				 FROM sitemap AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ? AND i.id != ?',
				array(BL::getWorkingLanguage(), $url, $id));

			// already exists
			if($number != 0)
			{
				// add number
				$url = BackendModel::addNumber($url);

				// try again
				return self::getUrl($url, $id);
			}
		}

		// return the unique Url!
		return $url;
	}

	/**
	 * Insert an item in the database
	 *
	 * @param array $data
	 * @return int
	 */
	public static function insert(array $data)
	{
		$data['created_on'] = BackendModel::getUTCDate();

		return (int) BackendModel::getDB(true)->insert('sitemap', $data);
	}

	/**
	 * Updates an item
	 *
	 * @param	array $data		The data to update.
	 * @param	int $itemId		The item id to update.
	 */
	public static function update(array $data, $itemId)
	{
		$data['edited_on'] = BackendModel::getUTCDate();

		BackendModel::getDB(true)->update('sitemap', $data, 'id = ?', (int) $itemId);
	}
}
