<?php

/**
 * In this file we store all generic functions that we will be using in the TagsModule
 *
 * @package		backend
 * @subpackage	tags
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dave Lens <dave@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendTagsModel
{
	/**
	 * Overview of all tags
	 *
	 * @var	string
	 */
	const QRY_DATAGRID_BROWSE = 'SELECT i.id, i.tag, i.number AS num_tags
									FROM tags AS i
									WHERE i.language = ?
									GROUP BY i.id';


	/**
	 * Delete one or more tags.
	 *
	 * @return	void
	 * @param 	mixed $ids	The ids to delete.
	 */
	public static function delete($ids)
	{
		// get db
		$db = BackendModel::getDB(true);

		// make sure $ids is an array
		$ids = (array) $ids;

		// delete tags
		$db->delete('tags', 'id IN ('. implode(',', $ids) .')');
		$db->delete('modules_tags', 'tag_id IN ('. implode(',', $ids) .')');
	}


	/**
	 * Check if a tag exists.
	 *
	 * @return	bool
	 * @param	int $id		The id to check for existence.
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT i.id
														FROM tags AS i
														WHERE i.id = ?',
														array((int) $id));
	}


	/**
	 * Get tag record.
	 *
	 * @return	array
	 * @param	int $id		The id of the record to get.
	 */
	public static function get($id)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.tag AS name
															FROM tags AS i
															WHERE i.id = ?',
															array((int) $id));
	}


	/**
	 * Get tags that start with the given string
	 *
	 * @return	array
	 * @param	string $term	The searchstring.
	 */
	public static function getStartsWith($term)
	{
		return (array) BackendModel::getDB()->getRecords('SELECT i.tag AS name, i.tag AS value
															FROM tags AS i
															WHERE i.tag LIKE ?
															ORDER BY i.tag ASC',
															array((string) $term .'%'));
	}


	/**
	 * Get tags for an item
	 *
	 * @return	mixed
	 * @param	string $module				The module wherin will be searched.
	 * @param	int $otherId				The id of the record.
	 * @param	string[optional] $type		The type of the returnvalue, possible values are: array, string (tags will be joined by ,).
	 * @param	string[optional] $language	The language to use, of not provided the working language will be used.
	 */
	public static function getTags($module, $otherId, $type = 'string', $language = null)
	{
		// redefine
		$module = (string) $module;
		$otherId = (int) $otherId;
		$type = (string) SpoonFilter::getValue($type, array('string', 'array'), 'string');
		$language = ($language != null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// fetch tags
		$tags = (array) BackendModel::getDB()->getColumn('SELECT i.tag
															FROM tags AS i
															INNER JOIN modules_tags AS mt ON i.id = mt.tag_id
															WHERE mt.module = ? AND mt.other_id = ? AND i.language = ?
															ORDER BY i.tag ASC',
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
	 * @param	string $URL			The URL to use as a base.
	 * @param	int[optional] $id	The ID to ignore.
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
			$number = (int) $db->getVar('SELECT COUNT(i.id)
											FROM tags AS i
											WHERE i.url = ? AND i.language = ?',
											array($URL, $language));

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
			// redefine
			$id = (int) $id;

			// get number of tags with the specified url
			$number = (int) $db->getVar('SELECT COUNT(i.id)
											FROM tags AS i
											WHERE i.url = ? AND i.language = ? AND i.id != ?',
											array($URL, $language, $id));

			// there are items so, call this method again.
			if($number != 0)
			{
				// add a number
				$URL = BackendModel::addNumber($URL);

				// recall this method, but with a new url
				$URL = self::getURL($URL, $id);
			}
		}

		// return the unique URL!
		return $URL;
	}


	/**
	 * Insert a new tag
	 *
	 * @return	int
	 * @param	string $tag						The data for the tag.
	 * @param	string[optional] $language		The language wherin the tag will be inserted, if not provided the workinglanguage will be used.
	 */
	public static function insert($tag, $language = null)
	{
		// redefine
		$tag = (string) $tag;
		$language = ($language != null) ? (string) $language : BackendLanguage::getWorkingLanguage();

		// build record
		$item['language'] = $language;
		$item['tag'] = $tag;
		$item['number'] = 0;
		$item['url'] = self::getURL($tag);

		// insert and return id
		return (int) BackendModel::getDB(true)->insert('tags', $item);
	}


	/**
	 * Save the tags
	 *
	 * @return	void
	 * @param	int $otherId				The id of the item to tag.
	 * @param	mixed $tags					The tags for the item.
	 * @param	string $module				The module wherin the item is located.
	 * @param	string[optional] $language	The language wherin the tags will be inserted, if not provided the workinglanguage will be used.
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

		// make sure the list of tags is unique
		$tags = array_unique($tags);

		// get db
		$db = BackendModel::getDB(true);

		// get current tags for item
		$currentTags = (array) $db->getPairs('SELECT i.tag, i.id
												FROM tags AS i
												INNER JOIN modules_tags AS mt ON i.id = mt.tag_id
												WHERE mt.module = ? AND mt.other_id = ? AND i.language = ?',
												array($module, $otherId, $language));

		// remove old links
		if(!empty($currentTags)) $db->delete('modules_tags', 'tag_id IN ('. implode(', ', array_values($currentTags)) .') AND other_id = ?', $otherId);

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
			$tagsAndIds = (array) $db->getPairs('SELECT i.tag, i.id
													FROM tags AS i
													WHERE i.tag IN ("'. implode('", "', $tags) .'") AND i.language = ?',
													array($language));

			// loop again and create tags that don't exist already
			foreach($tags as $tag)
			{
				// doesn' exist yet
				if(!isset($tagsAndIds[$tag]))
				{
					// insert tag
					$tagsAndIds[$tag] = self::insert($tag, $language);
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

		// add to search index
		if(method_exists('BackendSearchModel', 'editIndex')) BackendSearchModel::editIndex($module, $otherId, array('tags' => implode(' ', (array) $tags)));

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
	 * Remark: $tag['id'] should be available.
	 *
	 * @return	void
	 * @param	array $item		The new data for the tag
	 */
	public static function update($item)
	{
		return BackendModel::getDB(true)->update('tags', $item, 'id = ?', $item['id']);
	}
}

?>