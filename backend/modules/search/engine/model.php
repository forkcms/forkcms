<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the search module
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class BackendSearchModel
{
	const QRY_DATAGRID_BROWSE_SYNONYMS =
		'SELECT i.id, i.term, i.synonym
		 FROM search_synonyms AS i
		 WHERE i.language = ?';

	const QRY_DATAGRID_BROWSE_STATISTICS =
		'SELECT UNIX_TIMESTAMP(i.time) AS time, i.term, i.data
		 FROM search_statistics AS i
		 WHERE i.language = ?';

	/**
	 * @deprecated
	 * Add an index
	 *
	 * @param string $module The module wherin will be searched.
	 * @param int $otherId The id of the record.
	 * @param  array $fields A key/value pair of fields to index.
	 * @param string[optional] $language The frontend language for this entry.
	 */
	public static function addIndex($module, $otherId, array $fields, $language = null)
	{
		self::saveIndex($module, $otherId, $fields, $language);
	}

	/**
	 * Delete a synonym
	 *
	 * @param int $id The id of the item we want to delete.
	 */
	public static function deleteSynonym($id)
	{
		// delete synonym
		BackendModel::getDB(true)->delete('search_synonyms', 'id = ?', array((int) $id));

		// invalidate the cache for search
		self::invalidateCache();
	}

	/**
	 * @deprecated
	 * Edit an index
	 *
	 * @param string $module The module wherin will be searched.
	 * @param int $otherId The id of the record.
	 * @param  array $fields A key/value pair of fields to index.
	 * @param string[optional] $language The frontend language for this entry.
	 */
	public static function editIndex($module, $otherId, array $fields, $language = null)
	{
		self::saveIndex($module, $otherId, $fields, $language);
	}

	/**
	 * Check if a synonym exists
	 *
	 * @param int $id The id of the item we're looking for.
	 * @return bool
	 */
	public static function existsSynonymById($id)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(id)
			 FROM search_synonyms
			 WHERE id = ?',
			array((int) $id)
		);
	}

	/**
	 * Check if a synonym exists
	 *
	 * @param string $term The term we're looking for.
	 * @param int[optional] $exclude Exclude a certain id.
	 * @return bool
	 */
	public static function existsSynonymByTerm($term, $exclude = null)
	{
		if($exclude == null) return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(id)
			 FROM search_synonyms
			 WHERE term = ?',
			array((string) $term)
		);

		return (bool) BackendModel::getDB()->getVar(
			'SELECT COUNT(id)
			 FROM search_synonyms
			 WHERE term = ? AND id != ?',
			array((string) $term, (int) $exclude)
		);
	}

	/**
	 * Get modules search settings
	 *
	 * @return array
	 */
	public static function getModuleSettings()
	{
		return BackendModel::getDB()->getRecords(
			'SELECT module, searchable, weight
			 FROM search_modules',
			array(), 'module'
		);
	}

	/**
	 * Get a synonym
	 *
	 * @param int $id The id of the item we're looking for.
	 * @return array
	 */
	public static function getSynonym($id)
	{
		return (array) BackendModel::getDB()->getRecord(
			'SELECT *
			 FROM search_synonyms
			 WHERE id = ?',
			array((int) $id)
		);
	}

	/**
	 * Insert module search settings
	 *
	 * @param string $module The module wherin will be searched.
	 * @param string $searchable Is the module searchable?
	 * @param string $weight Weight of this module's results.
	 */
	public static function insertModuleSettings($module, $searchable, $weight)
	{
		// insert or update
		BackendModel::getDB(true)->execute(
			'INSERT INTO search_modules (module, searchable, weight)
			 VALUES (?, ?, ?)
			 ON DUPLICATE KEY UPDATE searchable = ?, weight = ?',
			array($module['module'], $searchable, $weight, $searchable, $weight)
		);

		// invalidate the cache for search
		self::invalidateCache();
	}

	/**
	 * Insert a synonym
	 *
	 * @param array $item The data to insert in the db.
	 * @return int
	 */
	public static function insertSynonym($item)
	{
		// insert into db
		$id = BackendModel::getDB(true)->insert('search_synonyms', $item);

		// invalidate the cache for search
		self::invalidateCache();

		// return insert id
		return $id;
	}

	/**
	 * Invalidate search cache
	 */
	public static function invalidateCache()
	{
		foreach(SpoonFile::getList(FRONTEND_CACHE_PATH . '/search/') as $file) SpoonFile::delete(FRONTEND_CACHE_PATH . '/search/' . $file);
	}

	/**
	 * Remove an index
	 *
	 * @param string $module The module wherin will be searched.
	 * @param int $otherId The id of the record.
	 * @param string[optional] $language The language to use.
	 */
	public static function removeIndex($module, $otherId, $language = null)
	{
		// module exists?
		if(!in_array('search', BackendModel::getModules())) return;

		// set language
		if(!$language) $language = BL::getWorkingLanguage();

		// delete indexes
		BackendModel::getDB(true)->delete('search_index', 'module = ? AND other_id = ? AND language = ?', array((string) $module, (int) $otherId, (string) $language));

		// invalidate the cache for search
		self::invalidateCache();
	}

	/**
	 * Edit an index
	 *
	 * @param string $module The module wherin will be searched.
	 * @param int $otherId The id of the record.
	 * @param  array $fields A key/value pair of fields to index.
	 * @param string[optional] $language The frontend language for this entry.
	 */
	public static function saveIndex($module, $otherId, array $fields, $language = null)
	{
		// module exists?
		if(!in_array('search', BackendModel::getModules())) return;

		// no fields?
		if(empty($fields)) return;

		// set language
		if(!$language) $language = BL::getWorkingLanguage();

		// get db
		$db = BackendModel::getDB(true);

		// insert search index
		foreach($fields as $field => $value)
		{
			// reformat value
			$value = strip_tags((string) $value);

			// update search index
			$db->execute(
				'INSERT INTO search_index (module, other_id, language, field, value, active)
				 VALUES (?, ?, ?, ?, ?, ?)
				 ON DUPLICATE KEY UPDATE value = ?, active = ?',
				array((string) $module, (int) $otherId, (string) $language, (string) $field, $value, 'Y', $value, 'Y')
			);
		}

		// invalidate the cache for search
		self::invalidateCache();
	}

	/**
	 * Update a synonym
	 *
	 * @param array $item The data to update in the db.
	 */
	public static function updateSynonym($item)
	{
		// update
		BackendModel::getDB(true)->update('search_synonyms', $item, 'id = ?', array($item['id']));

		// invalidate the cache for search
		self::invalidateCache();
	}
}
