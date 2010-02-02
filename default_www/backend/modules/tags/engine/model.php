<?php

/**
 * BackendTagsModel
 *
 * In this file we store all generic functions that we will be using in the TagsModule
 *
 *
 * @package		backend
 * @subpackage	tags
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendTagsModel
{
	const QRY_DATAGRID_BROWSE = 'SELECT
									t.id,
									t.tag,
									t.number AS num_tags
									FROM tags AS t
									LEFT OUTER JOIN modules_tags AS mt ON mt.tag_id = t.id
									WHERE t.language = ?
									GROUP BY t.id';


	/**
	 * Check if a tag exists
	 *
	 * @return	bool
	 * @param	int $id
	 */
	public static function exists($id)
	{
		// get db
		$db = BackendModel::getDB();

		// exists?
		return $db->getNumRows('SELECT id FROM tags WHERE id = ?;', (int) $id);
	}


	/**
	 * Get tags that start with the given string
	 *
	 * @return	array
	 * @param	string $query
	 * @param	int[optional] $limit
	 */
	public static function getStartsWith($query, $limit = 10)
	{
		// get db
		$db = BackendModel::getDB();

		// make the call
		return (array) $db->retrieve('SELECT t.tag AS name, t.tag AS value
										FROM tags AS t
										WHERE t.tag LIKE ?
										ORDER BY t.tag ASC
										LIMIT ?;',
										array((string) $query .'%', (int) $limit));
	}


	/**
	 * Get tag record
	 *
	 * @return	array
	 * @param	int $id
	 */
	public static function get($id)
	{
		// get db
		$db = BackendModel::getDB();

		// make the call
		return (array) $db->getRecord('SELECT t.tag AS name
										FROM tags AS t
										WHERE t.id = ?;', (int) $id);
	}


	/**
	 * Get tags for an item
	 *
	 * @return	mixed
	 * @param	string $module
	 * @param	int $otherId
	 * @param	string[optional] $type
	 * @param	string[optional] $language
	 */
	public static function getTags($module, $otherId, $type = 'string', $language = null)
	{
		// redefine
		$module = (string) $module;
		$otherId = (int) $otherId;
		$type = (string) SpoonFilter::getValue($type, array('string', 'array'), 'string');
		$language = ($language != null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// get db
		$db = BackendModel::getDB();

		// fetch tags
		$tags = (array) $db->getColumn('SELECT t.tag
										FROM tags AS t
										INNER JOIN modules_tags AS mt ON t.id = mt.tag_id
										WHERE mt.module = ? AND mt.other_id = ? AND t.language = ?
										ORDER BY tag ASC;',
										array($module, $otherId, $language));

		// return as an imploded string
		if($type == 'string') return implode(',', $tags);

		// return as array
		return $tags;
	}


	/**
	 * Get a unique URL for a tag
	 *
	 * @return	string
	 * @param	string $URL
	 * @param	int[optional] $id
	 */
	public static function getURL($URL, $id = null)
	{
		// redefine
		$URL = SpoonFilter::urlise((string) $URL);
		$language = BL::getWorkingLanguage();

		// get db
		$db = BackendModel::getDB();

		// no specific id
		if($id === null)
		{
			// get number of tags with the specified url
			$number = (int) $db->getNumRows('SELECT t.id
												FROM tags AS t
												WHERE t.url = ? AND t.language = ?;', array($URL, $language));

			// there are items so, call this method again.
			if($number != 0)
			{
				// add a number
				$URL = BackendModel::addNumber($URL);

				// recall this method, but with a new url
				$URL = self::getURL($URL, $id);
			}
		}

		// specific id given
		else
		{
			// get number of tags with the specified url
			$number = (int) $db->getNumRows('SELECT t.id
												FROM tags AS t
												WHERE t.url = ? AND t.id != ? AND t.language = ?;',
												array($URL, $id, $language));

			// there are items so, call this method again.
			if($number != 0)
			{
				// add a number
				$URL = BackendModel::addNumber($URL);

				// recall this method, but with a new url
				$URL = self::getURL($URL, $id);
			}
		}

		return $URL;
	}


	/**
	 * Insert a new tag
	 *
	 * @return	int
	 * @param	string $tag
	 * @param	string[optional] $language
	 */
	public static function insertTag($tag, $language = null)
	{
		// redefine
		$tag = (string) $tag;
		$language = ($language != null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// get db
		$db = BackendModel::getDB(true);

		// build record
		$record['language'] = $language;
		$record['tag'] = $tag;
		$record['number'] = 0;
		$record['url'] = self::getURL($tag);

		// insert
		return (int) $db->insert('tags', $record);
	}


	/**
	 * Save the tags
	 *
	 * @return	void
	 * @param	int $otherId
	 * @param	mixed $tags
	 * @param	string $module
	 * @param	string[optional] $language
	 */
	public static function saveTags($otherId, $tags, $module, $language = null)
	{
		// redefine
		$otherId = (int) $otherId;
		$tags = (is_array($tags)) ? (array) $tags : (string) $tags;
		$module = (string) $module;
		$language = ($language != null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// redefine the tags as an array
		if(!is_array($tags)) $tags = (array) explode(',', $tags);

		// get db
		$db = BackendModel::getDB(true);

		// get current tags for item
		$currentTags = (array) $db->getPairs('SELECT t.tag, t.id
												FROM tags AS t
												INNER JOIN modules_tags AS mt ON t.id = mt.tag_id
												WHERE mt.module = ? AND mt.other_id = ? AND t.language = ?;',
												array($module, $otherId, $language));

		// remove old links
		if(!empty($currentTags)) $db->delete('modules_tags', 'tag_id IN ('. implode(array_values(', ', $currentTags)) .')');

		// tags provided
		if(!empty($tags))
		{
			// loop tags
			foreach($tags as $key => $tag)
			{
				// cleanup
				$tag = trim($tag);

				// unset if the tag is empty
				if($tag == '') unset($tags[$key]);

				// reset value
				else $tags[$key] = $tag;
			}

			// get tag ids
			$tagsAndIds = (array) $db->getPairs('SELECT t.tag, t.id
													FROM tags AS t
													WHERE t.tag IN("'. implode('", "', $tags) .'") AND t.language = ?;', $language);

			// loop again and create tags that don't exist already
			foreach($tags as $tag)
			{
				// doesn' exist yet
				if(!isset($tagsAndIds[$tag]))
				{
					// insert tag
					$tagsAndIds[$tag] = self::insertTag($tag, $language);
				}
			}

			// init items to insert
			$rowsToInsert = array();

			// loop again
			foreach($tags as $tag)
			{
				// get tagId
				$tagId = (int) $tagsAndIds[$tag];

				// not linked before so increment the counter
				if(!isset($currentTags[$tag])) $db->execute('UPDATE tags SET number = number + 1 WHERE id = ?', $tagId);

				// add to insert array
				$rowsToInsert[] = array('module' => $module, 'tag_id' => $tagId, 'other_id' => $otherId);
			}

			// insert the rows at once if there are items to insert
			if(!empty($rowsToInsert)) $db->insert('modules_tags', $rowsToInsert);
		}

		// decrement number
		foreach($currentTags as $tag => $tagId)
		{
			// if the tag can't be found in the new tags we lower the number of tags by one
			if(array_search($tag, $tags) === false) $db->execute('UPDATE tags SET number = number - 1 WHERE id = ?', $tagId);
		}

		// remove all tags that don't have anything linked
		$db->delete('tags', 'number = ?', 0);
	}


	/**
	 * Update a tag
	 *
	 * @return	void
	 * @param	array $tag
	 */
	public static function updateTag($tag)
	{
		// get db
		$db = BackendModel::getDB(true);

		// insert
		$db->update('tags', $tag, 'id = ?', $tag['id']);
	}
}

?>