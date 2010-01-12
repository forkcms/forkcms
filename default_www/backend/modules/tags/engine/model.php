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
 * @since		2.0
 */
class BackendTagsModel
{
	/**
	 * Get tags that start with the given string
	 *
	 * @return	array
	 * @param	string $query
	 * @param	int[optional] $limit
	 */
	public static function getStartsWith($query, $limit = 10)
	{
		// redefine
		$query = (string) $query;
		$limit = (int) $limit;

		// get db
		$db = BackendModel::getDB();

		// make the call
		return (array) $db->retrieve('SELECT t.tag AS name, t.tag AS value
										FROM tags AS t
										WHERE t.tag LIKE ?
										ORDER BY t.tag ASC
										LIMIT ?;',
										array($query .'%', $limit));
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

		$tags = (array) $db->getColumn('SELECT t.tag
										FROM tags AS t
										INNER JOIN modules_tags AS mt ON t.id = mt.tag_id
										WHERE mt.module = ? AND mt.other_id = ? AND t.language = ?;',
										array($module, $otherId, $language));

		// return as an imploded string
		if($type == 'string') return implode(',', $tags);

		// return as array
		else return $tags;
	}


	/**
	 * Get a unique URL for a tag
	 *
	 * @return	string
	 * @param	string $url
	 * @param	int[optional] $id
	 */
	public static function getURL($url, $id = null)
	{
		// redefine
		$url = (string) $url;

		// get db
		$db = BackendModel::getDB();

		// no specific id
		if($id === null)
		{
			// get number of tags with the specified url
			$number = (int) $db->getNumRows('SELECT t.id
												FROM tags AS t
												WHERE t.url = ?;',
												array($url));

			// no items?
			if($number == 0) return $url;

			// there are items so, call this method again.
			else
			{
				// add a number
				$url = BackendModel::addNumber($url);

				// recall this method, but with a new url
				return self::getURL($url, $id);
			}
		}
		else
		{
			// get number of tags with the specified url
			$number = (int) $db->getNumRows('SELECT t.id
												FROM tags AS t
												WHERE t.url = ? AND t.id != ?;',
												array($url, $id));

			// no items?
			if($number == 0) return $url;

			// there are items so, call this method again.
			else
			{
				// add a number
				$url = BackendModel::addNumber($url);

				// recall this method, but with a new url
				return self::getURL($url, $id);
			}
		}
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
		$db = BackendModel::getDB();

		// build record
		$record['language'] = $language;
		$record['tag'] = SpoonFilter::htmlspecialchars($tag);
		$record['number'] = 0;
		$record['url'] = self::getURL(SpoonFilter::urlise($tag));

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

		// redefine the tags into an array
		if(!is_array($tags)) $tags = (array) explode(',', $tags);

		// get db
		$db = BackendModel::getDB();

		// get current tags for item
		$currentTags = (array) $db->getPairs('SELECT t.tag, t.id
												FROM tags AS t
												INNER JOIN modules_tags AS mt ON t.id = mt.tag_id
												WHERE mt.module = ? AND mt.other_id = ?;',
												array($module, $otherId));

		// remove old links
		$db->delete('modules_tags', 'module = ? AND other_id = ?', array($module, $otherId));

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
													WHERE t.tag IN("'. implode('", "', $tags) .'") AND t.language = ?;',
													array($language));

			// loop again and create tags that don't exist already
			foreach($tags as $tag)
			{
				if(!isset($tagsAndIds[$tag]))
				{
					// insert a tag
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
				if(!isset($currentTags[$tag])) $db->execute('UPDATE tags SET number = number + 1 WHERE id = ?', array($tagId));

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
			if(array_search($tag, $tags) === false) $db->execute('UPDATE tags SET number = number - 1 WHERE id = ?', array($tagId));
		}

		// remove all tags that don't have anything linked
		$db->delete('tags', 'number = ?', array(0));
	}
}

?>