<?php

/**
 * FrontendTagsModel
 *
 * In this file we store all generic functions
 *
 *
 * @package		frontend
 * @subpackage	tags
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class FrontendTagsModel
{
	/**
	 * Get tags for an item
	 *
	 * @return	array
	 * @param	string $module
	 * @param	int $otherId
	 */
	public static function getForItem($module, $otherId)
	{
		// redefine
		$module = (string) $module;
		$otherId = (int) $otherId;

		// get db
		$db = FrontendModel::getDB();

		// init var
		$return = array();

		// get tags
		$linkedTags = (array) $db->retrieve('SELECT t.tag AS name, t.url
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
			// add full url
			$row['full_url'] = $tagLink .'/'. $row['url'];

			// add
			$return[] = $row;
		}

		// return
		return $return;
	}


	public static function getForMultipleItems($module, array $otherIds)
	{
		// redefine
		$module = (string) $module;

		// get db
		$db = FrontendModel::getDB();

		// init var
		$return = array();

		// get tags
		$linkedTags = (array) $db->retrieve('SELECT mt.other_id, t.tag AS name, t.url
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
			// add full url
			$row['full_url'] = $tagLink .'/'. $row['url'];

			// add
			$return[$row['other_id']][] = $row;
		}

		// return
		return $return;
	}
}

?>