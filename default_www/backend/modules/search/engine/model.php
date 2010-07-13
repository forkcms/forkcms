<?php

/**
 * BackendSearchModel
 * In this file we store all generic functions that we will be using in the search module
 *
 * @package		backend
 * @subpackage	search
 *
 * @author 		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class BackendSearchModel
{
	const QRY_DATAGRID_BROWSE_SYNONYMS = 'SELECT i.id, i.term, i.synonym
											FROM search_synonyms AS i
											WHERE i.language = ?';

	const QRY_DATAGRID_BROWSE_STATISTICS = 'SELECT UNIX_TIMESTAMP(i.time) AS time, i.term, i.data
											FROM search_statistics AS i
											WHERE i.language = ?';


	/**
	 * Add an index
	 *
	 * @return	void
	 * @param	string $module				The module wherin will be searched.
	 * @param	int $otherId				The id of the record.
	 * @param 	array $fields				A key/value pair of fields to index.
	 */
	public static function addIndex($module, $otherId, array $fields, $language = null)
	{
		// module active?
		if(!in_array('search', BackendModel::getModules(true))) return;

		// no fields?
		if (empty($fields)) return;

		// clear cache
		self::clearCache();

		// set language
		if (!$language) $language = BL::getWorkingLanguage();

		// get db
		$db = BackendModel::getDB(true);

		// insert search index
		foreach($fields as $field => $value)
		{
			// reformat value
			$value = strip_tags((String) $value);

			// insert in db
			$db->insert('search_index', array('module' => (String) $module, 'other_id' => (int) $otherId, 'language' => (String) $language, 'field' => (String) $field, 'value' => $value, 'active' => 'Y'));
		}
	}


	/**
	 * Clear search cache
	 *
	 * @return void
	 */
	public static function clearCache()
	{
		// create template
		$tpl = new SpoonTemplate();

		// set cache directory
		$tpl->setCacheDirectory(FRONTEND_CACHE_PATH .'/cached_templates/search');

		// clear cache
		$tpl->clearCache();
	}


	/**
	 * Delete a synonym
	 *
	 * @return	void
	 * @param	int $id		The id of the item we want to delete.
	 */
	public static function deleteSynonym($id)
	{
		// clear cache
		self::clearCache();

		// delete synonym
		BackendModel::getDB(true)->delete('search_synonyms', 'id = ?', array((int) $id));
	}


	/**
	 * Edit an index
	 *
	 * @return	void
	 * @param	string $module			The module wherin will be searched.
	 * @param	int $otherId			The id of the record.
	 * @param 	array $fields			A key/value pair of fields to index.
	 */
	public static function editIndex($module, $otherId, array $fields, $language = null)
	{
		// module active?
		if(!in_array('search', BackendModel::getModules(true))) return;

		// no fields?
		if (empty($fields)) return;

		// clear cache
		self::clearCache();

		// set language
		if (!$language) $language = BL::getWorkingLanguage();

		// get db
		$db = BackendModel::getDB(true);

		// insert search index
		foreach($fields as $field => $value)
		{
			// reformat value
			$value = strip_tags((String) $value);

			// field already exists
			if((bool) $db->getVar('SELECT COUNT(module) FROM search_index WHERE module = ? AND other_id = ? AND language = ? AND field = ?' , array((String) $module, (int) $otherId, (String) $language, (String) $field)))
			{
				// update in db
				$db->update('search_index', array('value' => $value, 'active' => 'Y'), 'module = ? AND other_id = ? AND language = ? AND field = ?' , array((String) $module, (int) $otherId, (String) $language, (String) $field));
			}

			// new field
			else
			{
				// insert in db
				$db->insert('search_index', array('module' => (String) $module, 'other_id' => (int) $otherId, 'language' => (String) $language, 'field' => (String) $field, 'value' => $value, 'active' => 'Y'));
			}
		}
	}


	/**
	 * Check if a synonym exists
	 *
	 * @return	bool
	 * @param	int $id			The id of the item we're looking for.
	 */
	public static function existsSynonymById($id)
	{
		return (bool) BackendModel::getDB(false)->getVar('SELECT COUNT(id)
															FROM search_synonyms
															WHERE id = ?', array((int) $id));
	}


	/**
	 * Check if a synonym exists
	 *
	 * @return	bool
	 * @param	string $term				The term we're looking for.
	 * @param	int[optional] $id			exclude a certain id.
	 */
	public static function existsSynonymByTerm($term, $exclude = null)
	{
		if($exclude == null) return (bool) BackendModel::getDB(false)->getVar('SELECT COUNT(id)
																				FROM search_synonyms
																				WHERE term = ?', array((string) $term));

		return (bool) BackendModel::getDB(false)->getVar('SELECT COUNT(id)
															FROM search_synonyms
															WHERE term = ? AND id != ?', array((string) $term, (int) $exclude));
	}


	/**
	 * Get modules search settings
	 *
	 * @return	array
	 */
	public static function getModuleSettings()
	{
		return BackendModel::getDB(true)->retrieve('SELECT module, searchable, weight
													FROM search_modules', array(), 'module');
	}


	/**
	 * Get a synonym
	 *
	 * @return	array
	 * @param	int $id						The id of the item we're looking for.
	 */
	public static function getSynonym($id)
	{
		return (array) BackendModel::getDB(false)->getRecord('SELECT *
																FROM search_synonyms
																WHERE id = ?', array((int) $id));
	}


	/**
	 * Insert module search settings
	 *
	 * @return	void
	 * @param	string $module				The module wherin will be searched.
	 * @param	string $searchable			Is the module searchable?
	 * @param	string $weight				Weight of this module's results.
	 */
	public static function insertModuleSettings($module, $searchable, $weight)
	{
		// clear cache
		self::clearCache();

		// insert or update
		BackendModel::getDB(true)->execute('INSERT INTO search_modules (module, searchable, weight) VALUES (?, ?, ?)
											ON DUPLICATE KEY UPDATE searchable = ?, weight = ?', array($module['module'], $searchable, $weight, $searchable, $weight));
	}


	/**
	 * Insert a synonym
	 *
	 * @return	void
	 * @param	array $item					The data to insert in the db.
	 */
	public static function insertSynonym($item)
	{
		// clear cache
		self::clearCache();

		// insert into db
		BackendModel::getDB(true)->insert('search_synonyms', $item);
	}


	/**
	 * Update a synonym
	 *
	 * @return	void
	 * @param	array $item					The data to update in the db.
	 */
	public static function updateSynonym($id, $item)
	{
		// clear cache
		self::clearCache();

		BackendModel::getDB(true)->update('search_synonyms', $item, 'id = ?', array($id));
	}


	/**
	 * Remove an index
	 *
	 * @return	void
	 * @param	string $module				The module wherin will be searched.
	 * @param	int $otherId				The id of the record.
	 */
	public static function removeIndex($module, $otherId, $language = null)
	{
		// module active?
		if(!in_array('search', BackendModel::getModules(true))) return;

		// clear cache
		self::clearCache();

		// set language
		if(!$language) $language = BL::getWorkingLanguage();

		// delete indexes
		BackendModel::getDB(true)->delete('search_index', 'module = ? AND other_id = ? AND language = ?', array((String) $module, (int) $otherId, (String) $language));
	}
}

?>