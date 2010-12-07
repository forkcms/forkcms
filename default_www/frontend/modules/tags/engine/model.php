<?php

/**
 * FrontendTagsModel
 * In this file we store all generic functions
 *
 * @package		frontend
 * @subpackage	tags
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class FrontendTagsModel
{
	/**
	 * Fetch the list of all tags, ordered by their occurence
	 *
	 * @return	array
	 */
	public static function getAll()
	{
		// fetch items
		return (array) FrontendModel::getDB()->getRecords('SELECT t.tag AS name, t.url, t.number
															FROM tags AS t
															WHERE t.language = ? AND t.number > 0
															ORDER BY number DESC, t.tag', FRONTEND_LANGUAGE);
	}


	/**
	 * Get tags for an item
	 *
	 * @return	array
	 * @param	string $module		The module wherin the otherId occurs.
	 * @param	int $otherId		The id of the item.
	 */
	public static function getForItem($module, $otherId)
	{
		// redefine
		$module = (string) $module;
		$otherId = (int) $otherId;

		// init var
		$return = array();

		// get tags
		$linkedTags = (array) FrontendModel::getDB()->getRecords('SELECT t.tag AS name, t.url
																	FROM modules_tags AS mt
																	INNER JOIN tags AS t ON mt.tag_id = t.id
																	WHERE mt.module = ? AND mt.other_id = ?',
																	array($module, $otherId));

		// return
		if(empty($linkedTags)) return $return;

		// create link
		$tagLink = FrontendNavigation::getURLForBlock('tags', 'detail');

		// loop tags
		foreach($linkedTags as $row)
		{
			// add full URL
			$row['full_url'] = $tagLink .'/'. $row['url'];

			// add
			$return[] = $row;
		}

		// return
		return $return;
	}


	/**
	 * Get the tag-id for a given URL
	 *
	 * @return	int
	 * @param	string $URL		The URL to get the id for.
	 */
	public static function getIdByURL($URL)
	{
		// exists
		return (int) FrontendModel::getDB()->getVar('SELECT id
													FROM tags
													WHERE url = ?',
													(string) $URL);
	}


	/**
	 * Get tags for multiple items.
	 *
	 * @return	array
	 * @param	string $module		The module wherefor you want to retrieve the tags.
	 * @param 	array $otherIds		The ids for the items.
	 */
	public static function getForMultipleItems($module, array $otherIds)
	{
		// redefine
		$module = (string) $module;

		// get db
		$db = FrontendModel::getDB();

		// init var
		$return = array();

		// get tags
		$linkedTags = (array) $db->getRecords('SELECT mt.other_id, t.tag AS name, t.url
												FROM modules_tags AS mt
												INNER JOIN tags AS t ON mt.tag_id = t.id
												WHERE mt.module = ? AND mt.other_id IN('. implode(', ', $otherIds) .')',
												array($module));

		// return
		if(empty($linkedTags)) return $return;

		// create link
		$tagLink = FrontendNavigation::getURLForBlock('tags', 'detail');

		// loop tags
		foreach($linkedTags as $row)
		{
			// add full URL
			$row['full_url'] = $tagLink .'/'. $row['url'];

			// add
			$return[$row['other_id']][] = $row;
		}

		// return
		return $return;
	}


	/**
	 * Get the modules that used a tag.
	 *
	 * @return	array
	 * @param	int $tagId
	 */
	public static function getModulesForTag($tagId)
	{
		// get modules
		return (array) FrontendModel::getDB()->getColumn('SELECT module
															FROM modules_tags
															WHERE tag_id = ?
															GROUP BY module
															ORDER BY module ASC',
															(int) $tagId);
	}


	/**
	 * Fetch a specific tag name
	 *
	 * @return	string
	 * @param	int $id
	 */
	public static function getName($id)
	{
		return FrontendModel::getDB()->getVar('SELECT tag FROM tags WHERE id = ?', (int) $id);
	}


	/**
	 * Get all related items
	 *
	 * @param	int $id
	 * @param	int $moduleId
	 * @param	int $otherModuleId
	 * @param	int[optional] $limit
	 * @return	array
	 */
	public static function getRelatedItemsByTags($id, $module, $otherModule, $limit = 5)
	{
		return (array) FrontendModel::getDB()->getColumn('SELECT t2.other_id
														FROM modules_tags AS t
														INNER JOIN modules_tags AS t2 ON t.tag_id = t2.tag_id
														WHERE t.other_id = ? AND t.module = ? AND t2.module = ? AND t2.other_id != t.other_id
														GROUP BY t2.other_id
														ORDER BY COUNT(t2.tag_id) DESC
														LIMIT ?',
														array($id, $module, $otherModule, $limit));
	}
}

?>