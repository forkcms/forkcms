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
class BackendLocationModel
{
	const QRY_DATAGRID_BROWSE =
		'SELECT id, title, CONCAT(street, " ", number, ", ", zip, " ", city, ", ", country) AS address
		 FROM location
		 WHERE language = ?';

	/**
	 * Delete an item
	 *
	 * @param int $id The id of the record to delete.
	 */
	public static function delete($id)
	{
		// get db
		$db = BackendModel::getContainer()->get('database');

		// get item
		$item = self::get($id);

		// build extra
		$extra = array(
			'id' => $item['extra_id'],
			'module' => 'location',
			'type' => 'widget',
			'action' => 'location'
		);

		$db->delete('modules_extras', 'id = ? AND module = ? AND type = ? AND action = ?', array($extra['id'], $extra['module'], $extra['type'], $extra['action']));
		$db->delete('location', 'id = ? AND language = ?', array((int) $id, BL::getWorkingLanguage()));
		$db->delete('location_settings', 'map_id = ?', array((int) $id));
	}

	/**
	 * Check if an item exists
	 *
	 * @param int $id The id of the record to look for.
	 * @return bool
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getContainer()->get('database')->getVar(
			'SELECT 1
			 FROM location AS i
			 WHERE i.id = ? AND i.language = ?
			 LIMIT 1',
			array((int) $id, BL::getWorkingLanguage())
		);
	}

	/**
	 * Fetch a record from the database
	 *
	 * @param int $id The id of the record to fetch.
	 * @return array
	 */
	public static function get($id)
	{
		return (array) BackendModel::getContainer()->get('database')->getRecord(
			'SELECT i.*
			 FROM location AS i
			 WHERE i.id = ? AND i.language = ?',
			array((int) $id, BL::getWorkingLanguage())
		);
	}

	/**
	 * Fetch a record from the database
	 *
	 * @return array
	 */
	public static function getAll()
	{
		return (array) BackendModel::getContainer()->get('database')->getRecords(
			'SELECT i.*
			 FROM location AS i
			 WHERE i.language = ? AND i.show_overview = ?',
			array(BL::getWorkingLanguage(), 'Y')
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
		$serializedData = (string) BackendModel::getContainer()->get('database')->getVar(
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
		$mapSettings = (array) BackendModel::getContainer()->get('database')->getPairs(
			'SELECT s.name, s.value
			 FROM location_settings AS s
			 WHERE s.map_id = ?',
			array((int) $mapId)
		);

		foreach($mapSettings as $key => $value) $mapSettings[$key] = unserialize($value);

		return $mapSettings;
	}

	/**
	 * Insert an item
	 *
	 * @param array $item The data of the record to insert.
	 * @return int
	 */
	public static function insert($item)
	{
		$db = BackendModel::getContainer()->get('database');
		$item['created_on'] = BackendModel::getUTCDate();

		// build extra
		$extra = array(
			'module' => 'location',
			'type' => 'widget',
			'label' => 'Location',
			'action' => 'location',
			'data' => null,
			'hidden' => 'N',
			'sequence' => $db->getVar(
				'SELECT MAX(i.sequence) + 1
				 FROM modules_extras AS i
				 WHERE i.module = ?', array('location')
				)
			);
		if(is_null($extra['sequence']))
		{
			$extra['sequence'] = $db->getVar(
				'SELECT CEILING(MAX(i.sequence) / 1000) * 1000 FROM modules_extras AS i'
			);
		}

		// insert extra
		$item['extra_id'] = $db->insert('modules_extras', $extra);
		$extra['id'] = $item['extra_id'];

		// insert and return the new id
		$item['id'] = $db->insert('location', $item);

		// update extra (item id is now known)
		$extra['data'] = serialize(array(
			'id' => $item['id'],
			'extra_label' => SpoonFilter::ucfirst(BL::lbl('Location', 'core')) . ': ' . $item['title'],
			'language' => $item['language'],
			'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $item['id'])
		);
		$db->update('modules_extras', $extra, 'id = ? AND module = ? AND type = ? AND action = ?', array($extra['id'], $extra['module'], $extra['type'], $extra['action']));

		// return the new id
		return $item['id'];
	}

	/**
	 * Save the map settings
	 *
	 * @param int $mapId
	 * @param string $key
	 * @param mixed $value
	 */
	public static function setMapSetting($mapId, $key, $value)
	{
		$value = serialize($value);

		BackendModel::getContainer()->get('database')->execute(
			'INSERT INTO location_settings(map_id, name, value)
			 VALUES(?, ?, ?)
			 ON DUPLICATE KEY UPDATE value = ?',
			array((int) $mapId, $key, $value, $value)
		);
	}

	/**
	 * Update an item
	 *
	 * @param array $item The data of the record to update.
	 * @return int
	 */
	public static function update($item)
	{
		$db = BackendModel::getContainer()->get('database');
		$item['edited_on'] = BackendModel::getUTCDate();

		if(isset($item['extra_id']))
		{
			// build extra
			$extra = array(
				'id' => $item['extra_id'],
				'module' => 'location',
				'type' => 'widget',
				'label' => 'Location',
				'action' => 'location',
				'data' => serialize(array(
					'id' => $item['id'],
					'extra_label' => SpoonFilter::ucfirst(BL::lbl('Location', 'core')) . ': ' . $item['title'],
					'language' => $item['language'],
					'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $item['id'])
				),
				'hidden' => 'N'
			);

			// update extra
			$db->update('modules_extras', $extra, 'id = ? AND module = ? AND type = ? AND action = ?', array($extra['id'], $extra['module'], $extra['type'], $extra['action']));
		}

		// update item
		return $db->update('location', $item, 'id = ? AND language = ?', array($item['id'], $item['language']));
	}
}
