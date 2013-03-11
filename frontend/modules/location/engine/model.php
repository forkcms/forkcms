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
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class FrontendLocationModel
{
	/**
	 * This will build the url to google maps for a large map
	 *
	 * @param array $settings
	 * @param array $markers
	 * @return string
	 */
	public static function buildUrl(array $settings, array $markers = array())
	{
		$url = 'http://maps.google.be/?';

		// add the center point
		$url .= 'll=' . $settings['center']['lat'] . ',' . $settings['center']['lng'];

		// add the zoom level
		$url .= '&z=' . $settings['zoom_level'];

		// set the map type
		switch(strtolower($settings['map_type']))
		{
			case 'roadmap':
				$url .= '&t=m';
				break;
			case 'hybrid':
				$url .= '&t=h';
				break;
			case 'terrain':
				$url .= '&t=p';
				break;
			default:
				$url .= '&t=k';
				break;
		}

		$pointers = array();
		// add the markers to the url
		foreach($markers as $marker)
		{
			$pointers[] = urlencode($marker['title']) . '@' . $marker['lat'] . ',' . $marker['lng'];
		}

		if(!empty($pointers)) $url .= '&q=' . implode('|', $pointers);

		return $url;
	}

	/**
	 * Get an item
	 *
	 * @param int $id The id of the item to fetch.
	 * @return array
	 */
	public static function get($id)
	{
		return (array) FrontendModel::getContainer()->get('database')->getRecord(
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
		return (array) FrontendModel::getContainer()->get('database')->getRecords(
			'SELECT * FROM location WHERE language = ? AND show_overview = ?',
			array(FRONTEND_LANGUAGE, 'Y')
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
		$serializedData = (string) FrontendModel::getContainer()->get('database')->getVar(
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
		$mapSettings = (array) FrontendModel::getContainer()->get('database')->getPairs(
			'SELECT s.name, s.value
			 FROM location_settings AS s
			 WHERE s.map_id = ?',
			array((int) $mapId)
		);

		foreach($mapSettings as $key => $value) $mapSettings[$key] = unserialize($value);

		return $mapSettings;
	}
}
