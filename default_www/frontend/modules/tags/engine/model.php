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
	 * Calls a method that has to be implemented though the tags interface
	 *
	 * @param string $module
	 * @param string $class
	 * @param string $method
	 * @param mixed $parameter
	 * @return mixed
	 */
	public static function callFromInterface($module, $class, $method, $parameter = null)
	{
		// reflection of my class
		$reflection = new ReflectionClass($class);

		// check to see if the interface is implemented
		if($reflection->implementsInterface('FrontendTagsInterface'))
		{
			// return result
			return call_user_func(array($class, $method), $parameter);
		}

		// interface is not implemented
		else
		{
			// when debug is on throw an exception
			if(SPOON_DEBUG) throw new FrontendException('To use the tags module you need to implement the FrontendTagsInterface in the model of your module ('. $module .').');

			// when debug is off show a descent message
			else exit(SPOON_DEBUG_MESSAGE);
		}
	}


	/**
	 * Get the tag for a given URL
	 *
	 * @return	array
	 * @param	string $URL		The URL to get the tag for.
	 */
	public static function get($URL)
	{
		// exists
		return (array) FrontendModel::getDB()->getRecord('SELECT id, language, tag AS name, number, url
															FROM tags
															WHERE url = ?;',
															(string) $URL);
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
															ORDER BY number DESC, t.tag;', FRONTEND_LANGUAGE);
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
													WHERE url = ?;',
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
															ORDER BY module ASC;',
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
		return FrontendModel::getDB()->getVar('SELECT tag FROM tags WHERE id = ?;', (int) $id);
	}


	/**
	 * Get the IDs and number of shared tags of all related items, sorted by the number of tags shared by both items.
	 *
	 * @return  array                            The keys are the IDs. The values are the number of matches. Sorted in descending order of number of matches.
	 * @param   int $id                          The ID of the item to match against.
	 * @param   string $module                   The name of the module $id belongs to.
	 * @param   string $otherModule              The name of the module in which to go looking for matches.
	 * @param   int[optional] $limit             The maximum number of related IDs to return.
	 * @param   int[optional] $numMinimumMatches The minimum number of matches, i.e. the number of tags that are shared with $id.
	 */
	public static function getRelatedIdsAndNumberOfMatchesByTags($id, $module, $otherModule, $limit = 5, $numMinimumMatches = 1)
	{
		// get more results than the given limit to increase the pool when picking random IDs with the same number of matches
		$increasedLimit = (int) $limit * 3;

		// set the parameters
		$parameters = array(
			':id' => (int) $id,
			':module' => (string) $module,
			':otherModule' => (string) $otherModule,
			':limit' => $increasedLimit,
			':numMinimumMatches' => (int) $numMinimumMatches
		);

		// get the top $increasedLimit IDs and their number of matching tags
		$pairs = FrontendModel::getDB()->getPairs('SELECT t2.other_id, COUNT(t2.other_id) AS numMatches
		                                           FROM modules_tags AS t
		                                           INNER JOIN modules_tags AS t2 ON t.tag_id = t2.tag_id
		                                           WHERE t.module = :module AND t.other_id = :id AND t2.module = :otherModule AND t2.other_id != t.other_id
		                                           GROUP BY t2.other_id
		                                           HAVING numMatches >= :numMinimumMatches
		                                           ORDER BY numMatches DESC
		                                           LIMIT :limit',
		                                          $parameters);

		// create the array to group the IDs by the number of matching tags
		$idsPerNumMatches = array();

		// now really, really, really group the IDs by the number of matching tags
		foreach($pairs as $id => $numMatches)
		{
			$idsPerNumMatches[$numMatches][] = $id;
		}

		// create our result array
		$result = array();

		// try to get $limit items, preferring those with the highest number of matches
		foreach($idsPerNumMatches as $numMatches => $ids)
		{
			// determine the number of IDs we still need
			$numNeededIds = $limit - count($result);

			// randomise the IDs
			shuffle($ids);

			// get as many IDs as needed, or at least as many as possible
			for($i = 0; $i < $numNeededIds && $i < count($ids); $i++)
			{
				$result[$ids[$i]] = $numMatches;
			}

			// stop if we have enough (remember that we could have many more results than the original $limit)
			if(count($result) === $limit)
			{
				break;
			}
		}

		// return the result
		return $result;
	}


	/**
	 * Get the IDs of at most $limit related items, as determined by the number of tags shared by both items.
	 *
	 * @return  array                            The IDs of the related items, sorted in descending order of number of matches.
	 * @param   int $id                          The ID of the item to match against.
	 * @param   string $module                   The name of the module $id belongs to.
	 * @param   string $otherModule              The name of the module in which to go looking for matches.
	 * @param   int[optional] $limit             The maximum number of related IDs to return.
	 * @param   int[optional] $numMinimumMatches The minimum number of matches, i.e. the number of tags that are shared with $id.
	 */
	public static function getRelatedIdsByTags($id, $module, $otherModule, $limit = 5, $numMinimumMatches = 1)
	{
		// store this function's arguments (PHP 5.2 does not allow inlining this call as a function argument)
		$arguments = func_get_args();

		// return the IDs of the related items, which are the keys of the (ID, numMatches) pairs
		return array_keys(call_user_func_array(array('self', 'getRelatedIdsAndNumberOfMatchesByTags'), $arguments));

	}
}

?>
