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
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
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

	/**
	 * Retrieve a map setting
	 *
	 * @param int $mapId
	 * @param string $name
	 * @return mixed
	 */
	public static function getMapSetting($mapId, $name)
	{
		$serializedData = (string) FrontendModel::getDB()->getVar(
			'SELECT s.value
			 FROM location_settings AS s
			 WHERE s.map_id = ? AND s.name = ?',
			array((int) $mapId, (string) $name)
		);

		if($serializedData != null) return unserialize($serializedData);
		return false;
	}

	/**
	 * Fetch all the settings for a specific map
	 *
	 * @param int $mapId
	 * @return array
	 */
	public static function getMapSettings($mapId)
	{
		$mapSettings = (array) FrontendModel::getDB()->getPairs(
			'SELECT s.name, s.value
			 FROM location_settings AS s
			 WHERE s.map_id = ?',
			array((int) $mapId)
		);

		foreach($mapSettings as $key => $value) $mapSettings[$key] = unserialize($value);

		return $mapSettings;
	}
}
