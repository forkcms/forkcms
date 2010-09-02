<?php

/**
 * FrontendTagsModel
 * In this file we store all generic functions
 *
 * @package		frontend
 * @subpackage	tags
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class FrontendTagsModel
{
	public static function exists($URL)
	{
		// exists
		return (bool) (FrontendModel::getDB()->getNumRows('SELECT id FROM tags WHERE url = ? AND language = ?;', array((string) $URL, FRONTEND_LANGUAGE)) > 0);
	}


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
															ORDER BY number DESC;', FRONTEND_LANGUAGE);
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
																	WHERE mt.module = ? AND mt.other_id = ?;',
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


	public static function getIdByURL($URL)
	{
		// exists
		return (int) FrontendModel::getDB()->getVar('SELECT id
													FROM tags
													WHERE url = ?;',
													(string) $URL);
	}


	public static function getModulesForTag($tagId)
	{
		// get modules
		return (array) FrontendModel::getDB()->getColumn('SELECT module
															FROM modules_tags
															WHERE tag_id = ?
															GROUP BY module
															ORDER BY module ASC;',
															(int) $tagId);
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
												WHERE mt.module = ? AND mt.other_id IN('. implode(', ', $otherIds) .');',
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
}

?>